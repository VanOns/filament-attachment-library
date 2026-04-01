---
name: filament-attachment-library-development
description: Build and work with the Filament Attachment Library, including attachment uploading, browsing, model relationships, and form field integration.
---

# Filament Attachment Library Development

## When to use this skill

Use this skill when working with `van-ons/filament-attachment-library` — a Filament plugin that provides attachment uploading, browsing, and management within Filament admin panels.

## Features

- **Attachment Library Page**: Full-width Filament page (`AttachmentLibrary`) for browsing and managing uploaded files and directories.
- **AttachmentField**: A Filament form field component (`VanOns\FilamentAttachmentLibrary\Forms\Components\AttachmentField`) for attaching files to model columns or relationships.
- **HasAttachments trait**: Eloquent trait (`VanOns\LaravelAttachmentLibrary\Concerns\HasAttachments`) that adds an `attachments()` morphToMany relationship and `attachmentCollection()` scoped relationship helper.
- **FocalPointPicker**: Custom Filament field (`VanOns\FilamentAttachmentLibrary\Filament\Fields\FocalPointPicker`) for setting an image focal point.
- **File operations**: Built-in actions for delete, edit, move, rename, replace, and open on both attachments and directories.
- **Layouts**: Grid and list view toggle via the `Layout` enum (`VanOns\FilamentAttachmentLibrary\Enums\Layout`).
- **Glide integration**: Image scaling/resizing via the `<x-laravel-attachment-library-image>` Blade component (powered by Glide).
- **i18n**: English and Dutch translations included; publishable for customisation.

## File Structure

```
config/
  filament-attachment-library.php   # user_model, username_property, upload_rules
resources/
  lang/{en,nl}/                     # Translations: enums, forms, notifications, validation, views
  views/
    components/                     # Attachment & directory browser UI components
    forms/components/               # AttachmentField Blade template
    livewire/                       # AttachmentBrowser and AttachmentInfo Livewire templates
    pages/                          # Main attachments page layout
src/
  Actions/                          # Delete, Edit, Move, Rename, Replace, Open actions
  Enums/Layout.php                  # Grid/List layout enum
  Filament/
    Fields/FocalPointPicker.php
    Pages/AttachmentLibrary.php
  Forms/Components/AttachmentField.php
  Livewire/
    AttachmentBrowser.php
    AttachmentInfo.php
  Rules/                            # HasValidExtension, AllowedFilename, DestinationExists
  ViewModels/
    AttachmentViewModel.php
    DirectoryViewModel.php
  FilamentAttachmentLibrary.php     # Plugin entry point (implements Plugin)
  FilamentAttachmentLibraryServiceProvider.php
```

## Artisan Commands

- `php artisan filament-attachment-library:install` — Runs migrations, optionally installs `van-ons/laravel-attachment-library`, publishes config/views/translations, and runs `filament:assets`.
- `php artisan make:filament-theme [PANEL_NAME]` — (Filament core) Creates the custom theme required for TailwindCSS styling.

## Configuration

### Publish config, views, and translations

```bash
php artisan vendor:publish --tag=filament-attachment-library-config
php artisan vendor:publish --tag=filament-attachment-library-views
php artisan vendor:publish --tag=filament-attachment-library-translations
```

### config/filament-attachment-library.php

```php
return [
    'user_model'        => \Illuminate\Foundation\Auth\User::class,
    'username_property' => 'name',
    'upload_rules'      => [
        // Additional Laravel validation rules, e.g. 'extensions:jpg,png'
    ],
];
```

### Environment variable

```env
ATTACHMENTS_DISK=public  # Default: public — replace with your custom disk name if needed
```

> Use a dedicated disk to avoid file conflicts with other application assets.

### TailwindCSS (required)

Add to `resources/css/filament/[PANEL_NAME]/theme.css`:

```css
@source '../../../../vendor/van-ons/filament-attachment-library/resources/**/*.blade.php';
```

## Usage

### 1. Register the plugin in a Filament panel

```php
use VanOns\FilamentAttachmentLibrary\FilamentAttachmentLibrary;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            FilamentAttachmentLibrary::make()->navigationGroup('Files')
        );
}
```

### 2. Add HasAttachments to an Eloquent model

```php
use VanOns\LaravelAttachmentLibrary\Concerns\HasAttachments;

class Post extends Model
{
    use HasAttachments;

    // Optional: scoped collection relationship
    public function gallery(): MorphToMany
    {
        return $this->attachmentCollection('gallery');
    }
}
```

### 3. Use AttachmentField in a Filament form

```php
use VanOns\FilamentAttachmentLibrary\Forms\Components\AttachmentField;

// Store attachment ID in a model column
AttachmentField::make('featured_image'),

// Store in the attachments relationship (collection name = field name)
AttachmentField::make('gallery')->relationship(),

// Store in the attachments relationship with a custom collection name
AttachmentField::make('gallery')->relationship()->collection('product_gallery'),
```

### 4. Display an attachment as an image (Blade)

```blade
<x-laravel-attachment-library-image :src="$attachment" />
{{-- $attachment can be an Attachment model instance or an ID (string/int) --}}
```

## Best Practices & Common Pitfalls

- **Custom theme is mandatory**: Without a custom Filament theme and the `@source` directive, TailwindCSS utility classes from this package's Blade views will not be compiled and the UI will appear unstyled.
- **Dedicated storage disk**: Always use a disk that does not contain other application files to avoid accidental deletion or conflicts.
- **Run install command after composer require**: `php artisan filament-attachment-library:install` handles migrations and asset publishing in one step.
- **Collection names**: When using `->relationship()` without `->collection()`, the collection name defaults to the field name. Be consistent across model relationships and form fields to avoid mismatched queries.
- **Filament version**: v2 of this package requires Filament >=4.0. For Filament <4.0, use the `release/v1` branch.
- **Image resizing configuration**: Adjust `config/glide.php` (published by `van-ons/laravel-attachment-library`) for image dimension and quality settings used by the Blade image component.
