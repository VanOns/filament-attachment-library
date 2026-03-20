<?php

namespace VanOns\FilamentAttachmentLibrary;

use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use VanOns\FilamentAttachmentLibrary\Filament\Pages\AttachmentLibrary;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentInfo;

class FilamentAttachmentLibraryServiceProvider extends PackageServiceProvider
{
    public function registeringPackage(): void
    {
        // Register all livewire components
        Livewire::component('attachment-browser', AttachmentBrowser::class);
        Livewire::component('attachment-info', AttachmentInfo::class);

        // Register attachment browser modal on every page start
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_END,
            fn () => view('filament-attachment-library::components.attachment-browser-modal', [
                'basePath' => AttachmentLibrary::getBasePath(),
            ]),
        );
    }

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
