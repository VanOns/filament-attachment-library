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
    }
}
