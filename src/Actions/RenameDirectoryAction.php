<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use VanOns\FilamentAttachmentLibrary\Actions\Traits\HasCurrentPath;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class RenameDirectoryAction extends Action
{
    use HasCurrentPath;

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.directory.rename'));

        $this->outlined();

        $this->schema([
            TextInput::make('name')
                ->rules([
                    new DestinationExists($this->currentPath),
                    new AllowedFilename(),
                ]),
        ]);

        $this->mountUsing(function (Schema $schema, array $arguments) {
            $schema->fill([
                'name' => $arguments['name'],
            ]);
        });

        $this->action(function (array $arguments, array $data) {
            AttachmentManager::renameDirectory($arguments['full_path'], $data['name']);

            Notification::make()
                ->title(__('filament-attachment-library::notifications.directory.renamed'))
                ->success()
                ->send();
        });
    }
}
