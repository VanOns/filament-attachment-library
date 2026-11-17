<?php

namespace VanOns\FilamentAttachmentLibrary\Enums;

/**
 * How the attachment browser lets a user move between directories.
 */
enum DirectoryNavigation: string
{
    /**
     * Directory cards above the files of the current directory.
     */
    case CARDS = 'cards';

    /**
     * A collapsible directory tree beside the files.
     */
    case TREE = 'tree';

    /**
     * The navigation set in the config; anything unknown falls back to the cards.
     */
    public static function configured(): self
    {
        return self::tryFrom((string) config('filament-attachment-library.directory_navigation')) ?? self::CARDS;
    }

    public function isTree(): bool
    {
        return $this === self::TREE;
    }
}
