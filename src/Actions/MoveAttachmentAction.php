<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use VanOns\LaravelAttachmentLibrary\Exceptions\DestinationAlreadyExistsException;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class MoveAttachmentAction extends Action
{
    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.attachment.move'));

        $this->color('gray');

        $this->schema([
            Select::make('path')->options(
                fn () => $this->getDirectories(recursive: true)
                    ->sort()
                    ->prepend(null)
                    ->mapWithKeys(fn (?string $path) => [$path => '/' . $path])
            )
                ->selectablePlaceholder(true)
                ->placeholder('/')
                ->searchable(),
        ]);

        $this->mountUsing(function (Schema $schema, array $arguments) {
            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);
            $schema->fill(['path' => $attachment->path]);
        });

        $this->action(function (array $arguments, array $data) {
            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);

            try {
                AttachmentManager::move($attachment, $data['path'] ?? null);

                $this->getLivewire()->dispatch('refresh-attachments');

                Notification::make()
                    ->title(__('filament-attachment-library::notifications.attachment.moved'))
                    ->success()
                    ->send();
            } catch (DestinationAlreadyExistsException $e) {
                Notification::make()
                    ->title(__('filament-attachment-library::validation.destination_exists'))
                    ->danger()
                    ->send();
            }
        });

        $this->modalSubmitActionLabel(__('filament-attachment-library::views.actions.attachment.move'));
    }

    protected function getDirectories(?string $path = null, bool $recursive = false): Collection
    {
        $directories = AttachmentManager::directories($path)->pluck('fullPath');

        if ($recursive) {
            foreach ($directories as $directory) {
                $directories = $directories->merge($this->getDirectories($directory, recursive: true));
            }
        }

        return $directories;
    }
}
