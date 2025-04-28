# Installation

To get started with the Filament Attachment Library, you will need to follow a few steps. This guide will walk you through
the process of installing the package and setting it up in your Filament application.

## Composer

The Filament Attachment Library can be installed using Composer by running the following command:
```bash
$ composer require van-ons/filament-attachment-library
```

## Install command

A command is available that ensures that the migrations and assets are installed:

```bash
$ php artisan filament-attachment-library:install
```

## TailwindCSS

The templates in this package use TailwindCSS. To ensure the styling is rendered correctly, a custom Filament
theme must be set up, and the `tailwind.config.js` file should be extended.

Create the custom Filament theme and follow the instructions in the terminal to set it up:

```bash
php artisan make:filament-theme [PANEL_NAME]
```

Add the following to the generated `tailwind.config.js` file:

```javascript
// resources/css/filament/[PANEL_NAME]/tailwind.config.js
export default {
    presets: '',
    content: [
        // ...
        './vendor/van-ons/filament-attachment-library/resources/**/*.blade.php',
    ],
}
```

> [!NOTE]
> Make sure to follow the instructions in the terminal to register your custom Filament theme in the admin panel.
>
> If your project is using Vite, you may have to register the custom theme as follows:
> `->viteTheme('resources/css/filament/[PANEL_NAME]/theme.css', 'build')`

## Register plugin

Then, register the plugin in the desired Filament panel:

```php
<?php

namespace App\Providers\Filament;

use VanOns\FilamentAttachmentLibrary\FilamentAttachmentLibrary;

class ExamplePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->plugin(FilamentAttachmentLibrary::make());
    }

}
```

By default, this package uses the `public` disk defined in `filesystems.php`. This can be overridden by adding the following
to the project's `.env` file:

```env
ATTACHMENTS_DISK=disk_name_here
```

> [!NOTE]
> It is advised to use a disk without any other files. This prevents file conflicts.

The `glide.php` and `attachment-library.php` files contain more configuration options.
