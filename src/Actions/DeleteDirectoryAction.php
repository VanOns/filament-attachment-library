<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class DeleteDirectoryAction extends Action
{
    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.directory.delete'));

        $this->requiresConfirmation();

        $this->color('danger');

        $this->action(
            function (array $arguments) {
                AttachmentManager::deleteDirectory($arguments['directory']['fullPath']);

                Notification::make()
                    ->title(__('filament-attachment-library::notifications.directory.deleted'))
                    ->success()
                    ->send();
            }
        );
    }
}
