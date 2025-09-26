<?php

namespace VanOns\FilamentAttachmentLibrary;

use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAttachmentLibraryServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-attachment-library')
            ->hasConfigFile('filament-attachment-library')
            ->hasViews('filament-attachment-library')
            ->hasTranslations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->startWith(function (InstallCommand $callable) {
                    if ($callable->confirm('Would you like to install the van-ons/laravel-attachment-library?')) {
                        $callable->comment('Installing van-ons/laravel-attachment-library...');

                        $callable->call('attachment-library:install');
                    }

                    if ($callable->confirm('Would you like to publish the filament assets?')) {
                        $callable->comment('Publishing filament assets...');

                        $callable->call('filament:assets');
                    }
                })->setHidden(false);
            });
    }

    public function packageBooted(): void
    {
        FilamentAsset::registerScriptData([
            'fal' => [
                'labels' => [
                    'clipboardSuccess' => __('filament-attachment-library::notifications.clipboard.success'),
                ],
            ],
        ]);
    }
}
