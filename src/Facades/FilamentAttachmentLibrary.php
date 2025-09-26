<?php

namespace VanOns\FilamentAttachmentLibrary\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \VanOns\FilamentAttachmentLibrary\FilamentAttachmentLibrary
 */
class FilamentAttachmentLibrary extends Facade
{
    /**
     * Return the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \VanOns\FilamentAttachmentLibrary\FilamentAttachmentLibrary::class;
    }
}
