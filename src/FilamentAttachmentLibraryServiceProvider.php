<?php

namespace VanOns\FilamentAttachmentLibrary;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use VanOns\FilamentAttachmentLibrary\Filament\Pages\AttachmentLibrary;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentFieldUploader;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentInfo;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentModalStack;

class FilamentAttachmentLibraryServiceProvider extends PackageServiceProvider
{
    public function registeringPackage(): void
    {
        // Register all livewire components
        Livewire::component('attachment-browser', AttachmentBrowser::class);
        Livewire::component('attachment-field-uploader', AttachmentFieldUploader::class);
        Livewire::component('attachment-info', AttachmentInfo::class);
        Livewire::component('attachment-modal-stack', AttachmentModalStack::class);

        // Register the attachment browser modals on every page
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_END,
            fn () => Blade::render('<livewire:attachment-modal-stack :basePath="$basePath" />', [
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
        FilamentAsset::register([
            Js::make('filament-attachment-library', __DIR__ . '/../resources/dist/filament-attachment-library.js'),
        ], package: 'van-ons/filament-attachment-library');

        FilamentAsset::registerScriptData([
            'fal' => [
                'labels' => [
                    'clipboardSuccess' => __('filament-attachment-library::notifications.clipboard.success'),
                ],
            ],
        ]);
    }
}
