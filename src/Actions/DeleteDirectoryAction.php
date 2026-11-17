<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use VanOns\FilamentAttachmentLibrary\Actions\Traits\HasBasePath;
use VanOns\FilamentAttachmentLibrary\Support\Path;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class DeleteDirectoryAction extends Action
{
    use HasBasePath;

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.directory.delete'));

        $this->requiresConfirmation();

        // The tree can delete a directory that is not in view, so say which one and what goes with it.
        $this->modalDescription(fn (array $arguments) => __('filament-attachment-library::forms.delete_directory.description', [
            'directory' => Path::name((string) Path::sanitize($arguments['full_path'] ?? null)),
        ]));

        $this->color('danger');

        $this->action(
            function (array $arguments) {
                AttachmentManager::deleteDirectory($this->directoryBelowBasePath($arguments['full_path'] ?? null));

                Notification::make()
                    ->title(__('filament-attachment-library::notifications.directory.deleted'))
                    ->success()
                    ->send();
            }
        );
    }
}
