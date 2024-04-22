<?php

namespace VanOns\FilamentAttachmentLibrary\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AttachmentLibrary extends Page implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;
    use WithPagination;

    protected static ?string $navigationGroup = 'Media';

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament-attachment-library::pages.attachments';

    public static function getNavigationGroup(): ?string
    {
        return self::$navigationGroup;
    }

    public static function getNavigationSort(): ?int
    {
        return self::$navigationSort;
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
        return __('Bestandsbeheer');
    }

    public static function getSlug(): string
    {
        return __('bestandsbeheer');
    }
}
