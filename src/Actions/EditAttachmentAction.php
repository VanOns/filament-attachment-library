<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Arr;
use VanOns\FilamentAttachmentLibrary\Actions\Traits\HasCurrentPath;
use VanOns\FilamentAttachmentLibrary\Filament\Fields\FocalPointPicker;
use VanOns\FilamentAttachmentLibrary\Forms\Components\AttachmentField;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentInfo;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\FilamentAttachmentLibrary\Rules\ValidFocalPoint;
use VanOns\LaravelAttachmentLibrary\Enums\AttachmentType;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Facades\Ffmpeg;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class EditAttachmentAction extends Action
{
    use HasCurrentPath;

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.attachment.edit'));

        $this->color('gray');

        $this->schema(function (array $arguments) {
            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);

            $isImage = $attachment->isType(AttachmentType::PREVIEWABLE_IMAGE);
            $isVideo = $attachment->isType(AttachmentType::PREVIEWABLE_VIDEO);

            return [
                Grid::make()->schema([
                    Section::make(__('filament-attachment-library::forms.focal_point.label'))
                        ->description(__('filament-attachment-library::forms.focal_point.description'))
                        ->schema([
                            FocalPointPicker::make('focal_point')
                                ->hiddenLabel()
                                ->image($attachment->url)
                                ->rules([new ValidFocalPoint()]),
                        ])->visible($isImage),
                    Section::make(__('filament-attachment-library::forms.video.label'))
                        ->description(__('filament-attachment-library::forms.video.description'))
                        ->schema([
                            AttachmentField::make('poster_id')
                                ->label(__('filament-attachment-library::forms.video.poster'))
                                ->image()
                                ->helperText(null)
                                ->hintAction($this->generatePosterAction($attachment)),

                            Repeater::make('captions')
                                ->label(__('filament-attachment-library::forms.captions.label'))
                                ->addActionLabel(__('filament-attachment-library::forms.captions.add'))
                                ->defaultItems(0)
                                ->reorderable()
                                ->rule(fn () => function (string $attribute, mixed $value, Closure $fail) {
                                    if (collect($value)->where('is_default', true)->count() > 1) {
                                        $fail(__('filament-attachment-library::validation.single_default_caption'));
                                    }
                                })
                                ->schema([
                                    AttachmentField::make('caption_id')
                                        ->label(__('filament-attachment-library::forms.captions.file'))
                                        ->extensions(['vtt', 'srt'])
                                        ->helperText(null)
                                        ->required(),

                                    TextInput::make('language')
                                        ->label(__('filament-attachment-library::forms.captions.language'))
                                        ->helperText(__('filament-attachment-library::forms.captions.language_help'))
                                        ->required()
                                        ->maxLength(35),

                                    TextInput::make('label')
                                        ->label(__('filament-attachment-library::forms.captions.track_label'))
                                        ->helperText(__('filament-attachment-library::forms.captions.track_label_help'))
                                        ->maxLength(255),

                                    Toggle::make('is_default')
                                        ->label(__('filament-attachment-library::forms.captions.default')),
                                ]),
                        ])->visible($isVideo),
                    Section::make()->schema([
                        TextInput::make('name')
                            ->label(__('filament-attachment-library::forms.edit_attachment.name'))
                            ->rules([
                                new DestinationExists($this->currentPath, $arguments['attachment_id']),
                                new AllowedFilename(),
                            ], fn (?string $state) => $state !== $attachment->name)
                            ->maxLength(255),
                        TextInput::make('title')
                            ->label(__('filament-attachment-library::forms.edit_attachment.title'))
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label(__('filament-attachment-library::forms.edit_attachment.description'))
                            ->maxLength(255),
                        TextInput::make('alt')
                            ->hidden(! $isImage)
                            ->label(__('filament-attachment-library::forms.edit_attachment.alt'))
                            ->maxLength(255),
                        Textarea::make('caption')
                            ->hidden(! $isImage)
                            ->label(__('filament-attachment-library::forms.edit_attachment.caption'))
                            ->maxLength(255),
                    ])->contained(false),
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

        $this->modalWidth(Width::Full);
        $this->slideOver();
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
