<?php

namespace Tests;

use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use VanOns\FilamentAttachmentLibrary\FilamentAttachmentLibraryServiceProvider;
use VanOns\LaravelAttachmentLibrary\LaravelAttachmentLibraryServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            ActionsServiceProvider::class,
            SupportServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentServiceProvider::class,
            LaravelAttachmentLibraryServiceProvider::class,
            FilamentAttachmentLibraryServiceProvider::class,
        ];
    }

    protected function afterRefreshingDatabase(): void
    {
        $migrationsPath = __DIR__ . '/../vendor/van-ons/laravel-attachment-library/database/migrations';

        $migrations = [
            require "{$migrationsPath}/create_attachments_table.php.stub",
            require "{$migrationsPath}/add_focal_point_to_attachments_table.php.stub",
        ];

        foreach ($migrations as $migration) {
            $migration->up();
        }
    }
}
