<?php

namespace VanOns\FilamentAttachmentLibrary\Enums;

enum Layout: string
{
    case GRID = 'grid';
    case LIST = 'list';

    public function label(): string
    {
        return match ($this) {
            self::GRID => __('filament-attachment-library::enums.grid'),
            self::LIST => __('filament-attachment-library::enums.list'),
        };
    }

    public function isGrid(): bool
    {
        return $this === self::GRID;
    }

    public function isList(): bool
    {
        return $this === self::LIST;
    }
}
