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
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentInfo;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentInfoModal;
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
            // Stores
            Js::make('attachmentBrowser', __DIR__.'/../resources/js/stores/attachmentBrowser.js')->loadedOnRequest(),

            // Data
            Js::make('attachmentActions', __DIR__.'/../resources/js/data/attachmentActions.js'),
            Js::make('attachmentBrowserData', __DIR__.'/../resources/js/data/attachmentBrowser.js'),
            Js::make('attachmentBrowserField', __DIR__ . '/../resources/js/data/attachmentBrowserField.js'),
            Js::make('attachmentInfo', __DIR__.'/../resources/js/data/attachmentInfo.js'),
            Js::make('attachmentItem', __DIR__.'/../resources/js/data/attachmentItem.js'),
            Js::make('attachmentItemList', __DIR__.'/../resources/js/data/attachmentItemList.js'),
            Js::make('breadcrumbs', __DIR__.'/../resources/js/data/breadcrumbs.js'),
            Js::make('sidebar', __DIR__.'/../resources/js/data/sidebar.js'),

            // Directives
            Js::make('clipboard', __DIR__.'/../resources/js/directives/clipboard.js'),

            // Utils
            Js::make('i18n', __DIR__.'/../resources/js/utils/i18n.js'),
        ], $this->getId());
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
        Livewire::component('attachment-info', AttachmentInfo::class);
        Livewire::component('attachment-info-modal', AttachmentInfoModal::class);

        // Don't render attachment browser by default, only if needed
        View::share('renderAttachmentBrowserModal', false);

        // Register attachment browser modal on every page start
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_END,
            fn () => view('filament-attachment-library::livewire.attachment-browser-modal'),
        );

        // Load all synthesizers for livewire hydration
        Livewire::propertySynthesizer(DirectorySynth::class);
        Livewire::propertySynthesizer(AttachmentSynth::class);
    }
}
