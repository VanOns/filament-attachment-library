<?php

namespace VanOns\FilamentAttachmentLibrary\ViewModels;

use VanOns\LaravelAttachmentLibrary\DataTransferObjects\Directory;

class DirectoryViewModel
{
    public Directory $directory;

    public string $name;

    public string $fullPath;

    public function __construct(Directory $directory)
    {
        $this->directory = $directory;
        $this->name = $directory->name;
        $this->fullPath = $directory->fullPath;
    }

    public function isAttachment(): bool
    {
        return false;
    }

    public function isDirectory(): bool
    {
        return true;
    }

    public function isImage(): bool
    {
        return false;
    }

    public function isVideo(): bool
    {
        return false;
    }

    public function isDocument(): bool
    {
        return false;
    }

    public function isSelected(array $selected): bool
    {
        return in_array($this->directory, $selected);
    }

    public function thumbnailUrl(): ?string
    {
        return null;
    }
}
