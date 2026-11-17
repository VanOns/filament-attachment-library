<?php

namespace VanOns\FilamentAttachmentLibrary\Enums;

enum DirectoryNavigation: string
{
    case CARDS = 'cards';

    case TREE = 'tree';

    public static function configured(): self
    {
        return self::tryFrom((string) config('filament-attachment-library.directory_navigation')) ?? self::CARDS;
    }

    public function isTree(): bool
    {
        return $this === self::TREE;
    }
}
