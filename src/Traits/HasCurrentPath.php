<?php

namespace VanOns\FilamentAttachmentLibrary\Traits;

trait HasCurrentPath
{
    public ?string $currentPath = null;

    public function setCurrentPath(?string $path)
    {
        $this->currentPath = $path;

        return $this;
    }
}
