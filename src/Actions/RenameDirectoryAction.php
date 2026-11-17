<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use VanOns\FilamentAttachmentLibrary\Actions\Traits\HasBasePath;
use VanOns\FilamentAttachmentLibrary\Actions\Traits\HasCurrentPath;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\FilamentAttachmentLibrary\Support\Path;
use VanOns\LaravelAttachmentLibrary\Exceptions\DestinationAlreadyExistsException;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class RenameDirectoryAction extends Action
{
    use HasBasePath;
    use HasCurrentPath;

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.directory.rename'));

        $this->outlined();

        // Lazy, and from the directory that is renamed rather than the one that is open: the tree
        // renames directories anywhere, and the new name has to be free beside the old one.
        $this->schema(fn (array $arguments) => [
            TextInput::make('name')
                ->rules([
                    new DestinationExists(Path::parent((string) Path::sanitize($arguments['full_path'] ?? null))),
                    new AllowedFilename(),
                ]),
        ]);

        $this->mountUsing(function (Schema $schema, array $arguments) {
            $schema->fill([
                'name' => $arguments['name'],
            ]);
        });

        $this->action(function (array $arguments, array $data) {
            $path = $this->directoryBelowBasePath($arguments['full_path'] ?? null);

            try {
                AttachmentManager::renameDirectory($path, $data['name']);
            } catch (DestinationAlreadyExistsException) {
                Notification::make()
                    ->title(__('filament-attachment-library::validation.destination_exists'))
                    ->danger()
                    ->send();

                $this->halt();
            }

            Notification::make()
                ->title(__('filament-attachment-library::notifications.directory.renamed'))
                ->success()
                ->send();
        });
    }
}
