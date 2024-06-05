<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class DeleteDirectoryAction extends Action
{
    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.directory.delete'));

        $this->requiresConfirmation();

        $this->color('danger');

        $this->action(
            fn (array $arguments) => AttachmentManager::deleteDirectory($arguments['directory']['fullPath'])
        );
    }
}
