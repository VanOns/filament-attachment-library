<?php

namespace VanOns\FilamentAttachmentLibrary;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Livewire\Livewire;
use VanOns\FilamentAttachmentLibrary\Filament\Pages\AttachmentLibrary;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentInfo;

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

        // Register all livewire components
        Livewire::component('attachment-browser', AttachmentBrowser::class);
        Livewire::component('attachment-info', AttachmentInfo::class);

        // Register attachment browser modal on every page start
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_END,
            fn () => view('filament-attachment-library::components.attachment-browser-modal', [
                'basePath' => AttachmentLibrary::getBasePath(),
            ]),
        );
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
