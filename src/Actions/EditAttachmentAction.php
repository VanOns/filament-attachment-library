<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\HtmlString;
use VanOns\LaravelAttachmentLibrary\Enums\AttachmentType;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class EditAttachmentAction extends Action
{
    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.attachment.edit'));

        $this->color('gray');

        $this->form(function(array $arguments){
            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);

            $isImage = $attachment->isType(AttachmentType::PREVIEWABLE_IMAGE);

            return [
                TextInput::make('name')
                    ->label(new HtmlString(__('filament-attachment-library::forms.edit-attachment.name'))),
                TextInput::make('title')
                    ->label(new HtmlString(__('filament-attachment-library::forms.edit-attachment.title'))),
                Textarea::make('description')
                    ->label(__('filament-attachment-library::forms.edit-attachment.description')),
                TextInput::make('alt')
                    ->hidden(! $isImage)
                    ->label(new HtmlString(__('filament-attachment-library::forms.edit-attachment.alt'))),
                Textarea::make('caption')
                    ->hidden(! $isImage)
                    ->label(__('filament-attachment-library::forms.edit-attachment.caption')),
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
        });
    }
}
