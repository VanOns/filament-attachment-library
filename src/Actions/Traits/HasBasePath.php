<?php

namespace VanOns\FilamentAttachmentLibrary\Actions\Traits;

trait HasBasePath
{
    public ?string $basePath = null;

    public function setBasePath(?string $path): static
    {
        $this->basePath = $path;

        return $this;
    }
}
