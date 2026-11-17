<?php

namespace VanOns\FilamentAttachmentLibrary\Actions\Traits;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Cancel;
use VanOns\FilamentAttachmentLibrary\Support\Path;

trait HasBasePath
{
    public ?string $basePath = null;

    public function setBasePath(?string $path): static
    {
        $this->basePath = $path;

        return $this;
    }

    /**
     * The directory a client asked for by its path on the disk. Cancels the action unless that lies
     * below the base path: never the base path itself, never anything outside it.
     */
    protected function directoryBelowBasePath(?string $path): string
    {
        $path = Path::sanitize($path);

        if ($path !== null && Path::isBelow($path, $this->basePath)) {
            return $path;
        }

        Notification::make()
            ->title(__('filament-attachment-library::validation.invalid_path'))
            ->danger()
            ->send();

        throw new Cancel();
    }
}
