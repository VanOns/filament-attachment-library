<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\FilamentAttachmentLibrary\Traits\HasCurrentPath;
use VanOns\LaravelAttachmentLibrary\Enums\AttachmentType;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class EditAttachmentAction extends Action
{
    use HasCurrentPath;

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.attachment.edit'));

        $this->color('gray');

        $this->form(function (array $arguments) {
            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);

            $isImage = $attachment->isType(AttachmentType::PREVIEWABLE_IMAGE);

            return [
                TextInput::make('name')
                    ->label(__('filament-attachment-library::forms.edit_attachment.name'))
                    ->rules([
                        new DestinationExists($this->currentPath, $arguments['attachment_id']),
                        new AllowedFilename(),
                    ])
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
            ];
        });

        $this->mountUsing(function (ComponentContainer $form, array $arguments) {
            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);

            $form->fill([
                'alt' => $attachment->alt,
                'caption' => $attachment->caption,
                'description' => $attachment->description,
                'name' => $attachment->name,
                'title' => $attachment->title,
            ]);
        });

        $this->action(function (array $arguments, array $data) {
            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);

            if ($data['name'] !== $attachment->name) {
                AttachmentManager::rename($attachment, $data['name']);
            }

            $attachment->update($data);
            $attachment->save();

            $this->getLivewire()->dispatch('highlight-attachment', $arguments['attachment_id']);

            Notification::make()
                ->title(__('filament-attachment-library::notifications.attachment.updated'))
                ->success()
                ->send();
        });
    }
}
