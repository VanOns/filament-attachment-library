<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use VanOns\FilamentAttachmentLibrary\Support\Path;
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
                AttachmentManager::deleteDirectory(Path::sanitize($arguments['full_path']));

                Notification::make()
                    ->title(__('filament-attachment-library::notifications.directory.deleted'))
                    ->success()
                    ->send();
            }
        );
    }
}
