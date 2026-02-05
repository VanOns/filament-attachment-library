<?php

namespace VanOns\FilamentAttachmentLibrary;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Livewire\Livewire;
use VanOns\FilamentAttachmentLibrary\Filament\Pages\AttachmentLibrary;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentInfo;
use VanOns\FilamentAttachmentLibrary\Middleware\ConfigureAttachmentDirectory;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class FilamentAttachmentLibrary implements Plugin
{
    public static \Closure|string $directory;

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

        // Register all livewire components
        Livewire::component('attachment-browser', AttachmentBrowser::class);
        Livewire::component('attachment-info', AttachmentInfo::class);

        // Register attachment browser modal on every page start
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_END,
            fn () => view('filament-attachment-library::components.attachment-browser-modal'),
        );

        if (isset(self::$directory)) {
            $directory = self::$directory;
            if (is_string($directory)) {
                AttachmentManager::setDirectory($directory);
            } else {
                $panel->tenantMiddleware([
                    ConfigureAttachmentDirectory::class,
                ]);
            }
        }
    }

    public function navigationGroup(?string $navigationGroup): static
    {
        AttachmentLibrary::navigationGroup($navigationGroup);

        return $this;
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function boot(Panel $panel): void
    {
    }

    public function directory(\Closure|string $directory): static
    {
        self::$directory = $directory;

        return $this;
    }

    public static function handleSetDirectory(): void
    {
        if (isset(self::$directory) && is_callable(self::$directory)) {
            $directory = call_user_func(self::$directory);
            AttachmentManager::setDirectory($directory);
        }
    }
}
