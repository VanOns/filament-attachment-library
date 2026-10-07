<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use VanOns\FilamentAttachmentLibrary\Actions\Traits\HasCurrentPath;
use VanOns\FilamentAttachmentLibrary\Enums\AttachmentFieldLayout;
use VanOns\FilamentAttachmentLibrary\Filament\Fields\FocalPointPicker;
use VanOns\FilamentAttachmentLibrary\Forms\Components\AttachmentField;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentInfo;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\FilamentAttachmentLibrary\Rules\ValidFocalPoint;
use VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel;
use VanOns\LaravelAttachmentLibrary\Enums\AttachmentType;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Facades\Ffmpeg;
use VanOns\LaravelAttachmentLibrary\Facades\Resizer;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class EditAttachmentAction extends Action
{
    use HasCurrentPath;

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.attachment.edit'));

        $this->color('gray');

        // Registered as child actions so they open on top of the slide-over and can close it.
        $this->registerModalActions([
            fn (array $arguments, AttachmentBrowser|AttachmentInfo $livewire) => collect([
                ReplaceAttachmentAction::make('replace')->setCurrentPath($this->currentPath),
                MoveAttachmentAction::make('move')->setBasePath($livewire instanceof AttachmentBrowser ? $livewire->basePath : null),
                DeleteAttachmentAction::make('delete'),
            ])->map(fn (Action $action) => $action->arguments($arguments)->overlayParentActions())->all(),
        ]);

        $this->modalHeading(fn (array $arguments) => new HtmlString(view('filament-attachment-library::actions.edit-attachment.heading', [
            'attachment' => new AttachmentViewModel(Attachment::find($arguments['attachment_id'])),
        ])->render()));

        $this->schema(function (array $arguments) {
            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);
            $viewModel = new AttachmentViewModel($attachment);

            return [
                Grid::make(['default' => 1, 'lg' => 3])->schema([
                    Group::make([
                        ...$this->previewSchema($viewModel),
                        $this->fileSection($viewModel),
                    ])->columnSpan(['lg' => 1]),

                    Group::make([
                        $this->detailsSection($attachment),
                        $this->accessibilitySection()->visible($viewModel->isImage()),
                        $this->captionsSection()->visible($viewModel->isVideo()),
                    ])->columnSpan(['lg' => 2]),
                ]),
            ];
        });

        $this->mountUsing(function (Schema $schema, array $arguments) {
            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);

            $schema->fill([
                'alt' => $attachment->alt,
                'caption' => $attachment->caption,
                'description' => $attachment->description,
                'name' => $attachment->name,
                'title' => $attachment->title,
                'focal_point' => $attachment->focal_point,
                'poster_id' => $attachment->poster_id,
                'captions' => $attachment->captions->map(fn (Attachment $caption) => [
                    'caption_id' => $caption->id,
                    'language' => $caption->pivot->language,
                    'label' => $caption->pivot->label,
                    'is_default' => $caption->pivot->is_default,
                ])->all(),
            ]);
        });

        $this->action(function (array $arguments, array $data, AttachmentBrowser|AttachmentInfo $livewire) {
            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);

            if ($data['name'] !== $attachment->name) {
                AttachmentManager::rename($attachment, $data['name']);
            }

            $attachment->fill(Arr::except($data, ['captions']));
            $attachment->save();

            if ($attachment->isType(AttachmentType::PREVIEWABLE_VIDEO)) {
                AttachmentManager::syncCaptions($attachment, $data['captions'] ?? []);
            }

            $livewire->dispatchToScope('highlight-attachment', $arguments['attachment_id']);

            Notification::make()
                ->title(__('filament-attachment-library::notifications.attachment.updated'))
                ->success()
                ->send();
        });

        $this->modalSubmitActionLabel(__('filament-attachment-library::forms.edit_attachment.save'));
        $this->modalWidth(Width::FiveExtraLarge);
        $this->extraModalWindowAttributes(['class' => 'fal-edit-attachment-modal']);
        $this->slideOver();
    }

    /**
     * @return array<int, Component>
     */
    protected function previewSchema(AttachmentViewModel $viewModel): array
    {
        if ($viewModel->isImage()) {
            return [
                FocalPointPicker::make('focal_point')
                    ->label(__('filament-attachment-library::forms.focal_point.label'))
                    ->helperText(__('filament-attachment-library::forms.focal_point.description'))
                    ->image(Resizer::src($viewModel->attachment)->width(800)->resize()['url'] ?? $viewModel->url)
                    ->rules([new ValidFocalPoint()]),
            ];
        }

        if ($viewModel->isVideo()) {
            return [
                View::make('filament-attachment-library::actions.edit-attachment.video-preview')
                    ->viewData(['attachment' => $viewModel]),

                AttachmentField::make('poster_id')
                    ->label(__('filament-attachment-library::forms.video.poster'))
                    ->image()
                    ->layout(AttachmentFieldLayout::INPUT)
                    ->helperText(null)
                    ->hintAction($this->generatePosterAction($viewModel->attachment)),
            ];
        }

        return [
            View::make('filament-attachment-library::actions.edit-attachment.file-preview')
                ->viewData(['attachment' => $viewModel]),
        ];
    }

    protected function fileSection(AttachmentViewModel $viewModel): Section
    {
        $attachment = $viewModel->attachment;

        return Section::make(__('filament-attachment-library::forms.edit_attachment.sections.file'))
            ->compact()
            ->schema([
                TextEntry::make('file_dimensions')
                    ->label(__('filament-attachment-library::views.info.details.sections.image.dimensions'))
                    ->state(implode(' · ', array_filter([
                        $viewModel->dimensions ? str_replace('x', ' × ', $viewModel->dimensions) : null,
                        $viewModel->aspectRatioLabel(),
                    ])))
                    ->visible(filled($viewModel->dimensions)),

                TextEntry::make('file_duration')
                    ->label(__('filament-attachment-library::views.info.details.sections.video.duration'))
                    ->state($viewModel->duration)
                    ->visible(filled($viewModel->duration)),

                TextEntry::make('file_captions')
                    ->label(__('filament-attachment-library::views.info.details.sections.video.captions'))
                    ->state($attachment->captions->map(fn (Attachment $caption) => $caption->pivot->label ?: $caption->pivot->language)->implode(', '))
                    ->visible($viewModel->isVideo() && $attachment->captions->isNotEmpty()),

                TextEntry::make('file_type')
                    ->label(__('filament-attachment-library::views.info.details.mime_type'))
                    ->state($viewModel->mimeType)
                    ->visible($viewModel->isDocument()),

                TextEntry::make('file_path')
                    ->label(__('filament-attachment-library::views.info.details.path'))
                    ->state($viewModel->path ?: __('filament-attachment-library::views.edit.root')),
            ])
            ->inlineLabel();
    }

    protected function detailsSection(Attachment $attachment): Section
    {
        return Section::make(__('filament-attachment-library::forms.edit_attachment.sections.details'))
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-attachment-library::forms.edit_attachment.file_name'))
                    ->suffix('.' . $attachment->extension)
                    ->required()
                    ->rules([
                        new DestinationExists($this->currentPath, $attachment->id),
                        new AllowedFilename(),
                    ], fn (?string $state) => $state !== $attachment->name)
                    ->maxLength(255),

                TextInput::make('title')
                    ->label(__('filament-attachment-library::forms.edit_attachment.title'))
                    ->maxLength(255),

                Textarea::make('description')
                    ->label(__('filament-attachment-library::forms.edit_attachment.description'))
                    ->rows(2)
                    ->maxLength(255),
            ]);
    }

    protected function accessibilitySection(): Section
    {
        return Section::make(__('filament-attachment-library::forms.edit_attachment.sections.accessibility'))
            ->description(__('filament-attachment-library::forms.edit_attachment.sections.accessibility_description'))
            ->afterHeader([
                Text::make(__('filament-attachment-library::forms.edit_attachment.alt_missing'))
                    ->badge()
                    ->color('warning')
                    ->icon(Heroicon::OutlinedExclamationTriangle)
                    ->visible(fn (Get $get) => blank($get('alt'))),
            ])
            ->schema([
                Textarea::make('alt')
                    ->label(__('filament-attachment-library::forms.edit_attachment.alt'))
                    ->helperText(__('filament-attachment-library::forms.edit_attachment.alt_help'))
                    ->hint(fn (?string $state) => mb_strlen((string) $state) . ' / 255')
                    ->live(debounce: 500)
                    ->rows(2)
                    ->maxLength(255),

                Textarea::make('caption')
                    ->label(__('filament-attachment-library::forms.edit_attachment.caption'))
                    ->helperText(__('filament-attachment-library::forms.edit_attachment.caption_help'))
                    ->rows(2)
                    ->maxLength(255),
            ]);
    }

    protected function captionsSection(): Section
    {
        return Section::make(__('filament-attachment-library::forms.captions.label'))
            ->description(__('filament-attachment-library::forms.captions.description'))
            ->schema([
                Repeater::make('captions')
                    ->hiddenLabel()
                    ->addActionLabel(__('filament-attachment-library::forms.captions.add'))
                    ->defaultItems(0)
                    ->reorderable()
                    ->rule(fn () => function (string $attribute, mixed $value, Closure $fail) {
                        if (collect($value)->where('is_default', true)->count() > 1) {
                            $fail(__('filament-attachment-library::validation.single_default_caption'));
                        }
                    })
                    ->table([
                        TableColumn::make(__('filament-attachment-library::forms.captions.file'))->markAsRequired(),
                        TableColumn::make(__('filament-attachment-library::forms.captions.language'))->markAsRequired()->width('5rem'),
                        TableColumn::make(__('filament-attachment-library::forms.captions.track_label'))->width('9rem'),
                        TableColumn::make(__('filament-attachment-library::forms.captions.default'))->width('4.5rem'),
                    ])
                    ->schema([
                        AttachmentField::make('caption_id')
                            ->extensions(['vtt', 'srt'])
                            ->layout(AttachmentFieldLayout::INPUT)
                            ->helperText(null)
                            ->required(),

                        TextInput::make('language')
                            ->placeholder('en')
                            ->required()
                            ->maxLength(35),

                        TextInput::make('label')
                            ->maxLength(255),

                        Toggle::make('is_default'),
                    ]),
            ]);
    }

    protected function generatePosterAction(Attachment $video): Action
    {
        return Action::make('generatePoster')
            ->label(__('filament-attachment-library::forms.video.generate_poster'))
            ->icon('heroicon-o-film')
            ->visible(fn () => Ffmpeg::isAvailable())
            ->action(function (Set $set) use ($video) {
                $poster = AttachmentManager::generatePoster($video);

                if (!$poster) {
                    Notification::make()
                        ->title(__('filament-attachment-library::notifications.attachment.poster_failed'))
                        ->danger()
                        ->send();

                    return;
                }

                $set('poster_id', $poster->id);

                Notification::make()
                    ->title(__('filament-attachment-library::notifications.attachment.poster_generated'))
                    ->success()
                    ->send();
            });
    }
}
