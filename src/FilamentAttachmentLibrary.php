<?php

namespace VanOns\FilamentAttachmentLibrary;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use VanOns\FilamentAttachmentLibrary\Filament\Pages\AttachmentLibrary;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentItemList;
use VanOns\FilamentAttachmentLibrary\Livewire\Synthesizers\AttachmentSynth;
use VanOns\FilamentAttachmentLibrary\Livewire\Synthesizers\DirectorySynth;

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

        // Register all package panel assets
        $panel->assets([
            Js::make('attachmentBrowser', __DIR__.'/../resources/js/attachmentBrowser.js')->loadedOnRequest(),
        ]);
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function boot(Panel $panel): void
    {
        // Register all livewire components
        Livewire::component('attachment-browser', AttachmentBrowser::class);
        Livewire::component('attachment-item-list', AttachmentItemList::class);

        // Don't render attachment browser by default, only if needed
        View::share('renderAttachmentBrowserModal', false);

        // Register attachment browser modal on every page start
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_START,
            fn () => view('filament-attachment-library::livewire.attachment-browser-modal'),
        );

        // Load all synthesizers for livewire hydration
        Livewire::propertySynthesizer(DirectorySynth::class);
        Livewire::propertySynthesizer(AttachmentSynth::class);
    }
}
