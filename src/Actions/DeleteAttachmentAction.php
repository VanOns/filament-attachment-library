<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class DeleteAttachmentAction extends Action
{
    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.attachment.delete'));

        $this->requiresConfirmation();

        $this->color('danger');

        $this->action(function (array $arguments) {
            $this->getLivewire()->dispatch('dehighlight-attachment', $arguments['attachment_id']);

            AttachmentManager::delete(Attachment::find($arguments['attachment_id']));

            Notification::make()
                ->title(__('filament-attachment-library::notifications.attachment.deleted'))
                ->success()
                ->send();
        });
    }
}
