<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class DeleteAttachmentAction extends Action
{
    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.attachment.delete'));

        // Closes the edit slide-over this may be opened from, whose form would otherwise be stale.
        $this->cancelParentActions();

        $this->requiresConfirmation();

        $this->color('danger');

        $this->action(function (array $arguments, AttachmentBrowser $livewire) {
            $livewire->dispatchToScope('dehighlight-attachment', $arguments['attachment_id']);

            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);

            AttachmentManager::delete($attachment);

            Notification::make()
                ->title(__('filament-attachment-library::notifications.attachment.deleted'))
                ->success()
                ->send();
        });
    }
}
