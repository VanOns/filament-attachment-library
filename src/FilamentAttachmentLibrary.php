<?php

namespace VanOns\FilamentAttachmentLibrary;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use VanOns\FilamentAttachmentLibrary\Filament\Pages\AttachmentLibrary;

class FilamentAttachmentLibrary implements Plugin
{
    public function getId(): string
    {
        return 'van-ons/filament-attachment-library';
    }

    public function register(Panel $panel): void
    {
        // Register all package panel assets
        $panel->pages([
            AttachmentLibrary::class,
        ]);
    }

    public function navigationGroup(?string $navigationGroup): static
    {
        AttachmentLibrary::navigationGroup($navigationGroup);

        return $this;
    }

    public function basePath(null|Closure|string $basePath): static
    {
        AttachmentLibrary::basePath($basePath);

        return $this;
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function boot(Panel $panel): void
    {
    }
}
