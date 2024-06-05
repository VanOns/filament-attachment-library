<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\FilamentAttachmentLibrary\Traits\HasCurrentPath;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class RenameDirectoryAction extends Action
{
    use HasCurrentPath;

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.directory.rename'));

        $this->outlined();

        $this->form([
            TextInput::make('name')
                ->rules([
                    new DestinationExists($this->currentPath),
                    new AllowedFilename(),
                ]),
        ]);

        $this->mountUsing(function (ComponentContainer $form, array $arguments) {
            $form->fill([
                'name' => $arguments['directory']['name'],
            ]);
        });

        $this->action(function (array $arguments, array $data) {
            AttachmentManager::renameDirectory($arguments['directory']['fullPath'], $data['name']);
        });
    }
}
