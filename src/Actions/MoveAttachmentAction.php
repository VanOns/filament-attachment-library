<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use VanOns\FilamentAttachmentLibrary\Actions\Traits\HasBasePath;
use VanOns\FilamentAttachmentLibrary\Support\Path;
use VanOns\LaravelAttachmentLibrary\Exceptions\DestinationAlreadyExistsException;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class MoveAttachmentAction extends Action
{
    use HasBasePath;

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.attachment.move'));

        $this->color('gray');

        // Lazy: setUp() runs inside make(), before setBasePath() — the options must not
        // capture $this->basePath until the schema is actually resolved.
        $this->schema(fn () => [
            Select::make('path')->label(__('filament-attachment-library::views.info.details.path'))->options(
                fn () => $this->getDirectories($this->basePath, recursive: true)
                    ->sort()
                    // The base itself, so "move to the top level" stays inside the base path.
                    ->prepend($this->basePath)
                    ->mapWithKeys(fn (?string $path) => [$path => '/' . $this->relativePath($path)])
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

            // A null path means the placeholder was picked; that is the base path, not the disk root.
            $path = Path::sanitize($data['path'] ?? null) ?? Path::sanitize($this->basePath);

            try {
                AttachmentManager::move($attachment, $path);

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

    /**
     * Strip the base path so options read as paths relative to it rather than exposing the prefix.
     */
    protected function relativePath(?string $path): string
    {
        return blank($this->basePath)
            ? trim((string) $path, '/')
            : trim(Str::after((string) $path, $this->basePath), '/');
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
