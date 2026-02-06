<?php

namespace VanOns\FilamentAttachmentLibrary\Filament\Pages;

use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use UnitEnum;

class AttachmentLibrary extends Page implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;
    use WithPagination;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-folder';

    protected string $view = 'filament-attachment-library::pages.attachments';

    protected Width | string | null $maxContentWidth = Width::Full;

    protected static string | UnitEnum | null $navigationGroup = null;

    protected static null|Closure|string $basePath = null;

    public static function basePath(null|Closure|string $basePath): void
    {
        static::$basePath = $basePath;
    }

    public static function getBasePath(): ?string
    {
        return is_callable($basePath = static::$basePath)
            ? call_user_func($basePath)
            : $basePath;
    }

    public static function navigationGroup(?string $group): void
    {
        static::$navigationGroup = $group;
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

    protected function getViewData(): array
    {
        return [
            'basePath' => static::getBasePath(),
        ];
    }
}
