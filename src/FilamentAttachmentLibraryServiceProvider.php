<?php

namespace VanOns\FilamentAttachmentLibrary;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAttachmentLibraryServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('van-ons/filament-attachment-library')
            ->hasConfigFile('filament-attachment-library')
            ->hasViews('filament-attachment-library')
            ->hasTranslations();;
    }
}
