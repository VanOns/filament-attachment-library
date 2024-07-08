# Installation

To get started with the Filament Attachment Library, you will need to follow a few steps. This guide will walk you through the process of installing the package and setting it up in your Filament application.

## Composer

The Filament Attachment Library can be installed using composer by running the following command:
```bash
$ composer require van-ons/filament-attachment-library
```

## Install command

A command is available that ensures that the migrations and assets are installed:

```bash
$ php artisan filament-attachment-library:install
```

## TailwindCSS

The templates in this package use TailwindCSS. To ensure that the styling is rendered correctly, the `tailwind.config.js` file should be extended with the following:

```javascript
// tailwind.config.js
export default {
    presets: '',
    content: [
        // ...
        './vendor/van-ons/filament-attachment-library/resources/**/*.blade.php',
    ],
}
```

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

By default, this package uses the `public` disk defined in `filesystems.php`. This can be overridden by adding the following to the project's `.env` file:

> [!NOTE]
> It is advised to use a disk without any other files. This prevents file conflicts.

```env
ATTACHMENTS_DISK=disk_name_here
```

The `glide.php` and `attachment-library.php` files contain more configuration options.
