<?php

use Illuminate\Support\ServiceProvider;
use VanOns\FilamentAttachmentLibrary\FilamentAttachmentLibraryServiceProvider;

it('publishes the translations under the -lang tag as well', function () {
    expect(ServiceProvider::pathsToPublish(FilamentAttachmentLibraryServiceProvider::class, 'filament-attachment-library-lang'))
        ->not->toBeEmpty()
        ->toBe(ServiceProvider::pathsToPublish(FilamentAttachmentLibraryServiceProvider::class, 'filament-attachment-library-translations'));
});
