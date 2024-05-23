<?php

namespace VanOns\FilamentAttachmentLibrary\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AttachmentLibrary extends Page implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static string $view = 'filament-attachment-library::pages.attachments';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-attachment-library::views.group');
    }

    public static function getNavigationIcon(): ?string
    {
        return self::$navigationIcon;
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return self::$activeNavigationIcon;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-attachment-library::views.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament-attachment-library::views.title');
    }
}
