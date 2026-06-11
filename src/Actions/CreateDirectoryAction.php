<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use VanOns\FilamentAttachmentLibrary\Actions\Traits\HasCurrentPath;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\LaravelAttachmentLibrary\Enums\DirectoryStrategies;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class CreateDirectoryAction extends Action
{
    use HasCurrentPath;

    protected bool $hasBasePath = false;

    public function setHasBasePath(bool $hasBasePath): static
    {
        $this->hasBasePath = $hasBasePath;

        return $this;
    }

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.directory.create'));

        $this->icon('heroicon-o-folder-plus');

        $this->color('gray');

        $this->modalHeading(__('filament-attachment-library::forms.create_directory.heading'));

        $this->schema([
            TextInput::make('name')
                ->rules([
                    new DestinationExists($this->currentPath),
                    new AllowedFilename(),
                ])
                ->required()
                ->autocomplete(false)
                ->label(__('filament-attachment-library::forms.create_directory.name')),
        ]);

        $this->modalSubmitActionLabel(__('filament-attachment-library::views.actions.directory.create'));

        $this->action(function (array $data) {
            $path = implode('/', array_filter([$this->currentPath, $data['name']]));

            $flags = [];
            if ($this->hasBasePath) {
                $flags[] = DirectoryStrategies::CREATE_PARENT_DIRECTORIES;
            }

            AttachmentManager::createDirectory($path, ...$flags);

            Notification::make()
                ->title(__('filament-attachment-library::notifications.directory.created'))
                ->success()
                ->send();
        });
    }
}
