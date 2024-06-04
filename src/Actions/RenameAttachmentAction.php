<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\FilamentAttachmentLibrary\Traits\HasCurrentPath;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

final class RenameAttachmentAction extends Action
{
    use HasCurrentPath;

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.attachment.rename'));

        $this->color('gray');

        $this->form(function (array $arguments) {
            return [
                TextInput::make('name')->rules([
                    new DestinationExists($this->currentPath, $arguments['attachment_id']),
                    new AllowedFilename(),
                ]),
            ];
        });

        $this->mountUsing(function (ComponentContainer $form, array $arguments) {
            $form->fill([
                'name' => Attachment::find($arguments['attachment_id'])->name,
            ]);
        });

        $this->action(function (array $arguments, array $data) {
            $attachment = Attachment::find($arguments['attachment_id']);

            AttachmentManager::rename($attachment, $data['name']);

            $this->getLivewire()->dispatch('highlight-attachment', $arguments['attachment_id']);
        });
    }
}
