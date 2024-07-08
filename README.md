<p align="center"><img src="art/social-card.png" alt="Social card of Filament attachment library"></p>

# Filament attachment library

<!-- BADGES -->

Filament package for easy attachment uploading and browsing.

## Quick start

### Installation

The Filament Attachment Library can be installed using composer by running the following command:

```bash
$ composer require van-ons/filament-attachment-library
```

An installation command is available that ensures that the migrations and assets are installed:

```bash
$ php artisan filament-attachment-library:install
```

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

### Usage

In your form schema, add the AttachmentField:

```php
use Filament\Forms;
use Filament\Forms\Form;
use VanOns\FilamentAttachmentLibrary\Forms\Components\AttachmentField;
 
public static function form(Form $form): Form
{
    return $form
        ->schema([
            // ...
            AttachmentField::make('attachment'),
        ]);
}
```

## Documentation

Please see the [documentation] for detailed information about installation and usage.

## Contributing

Please see [contributing] for more information about how you can contribute.

## Changelog

Please see [changelog] for more information about what has changed recently.

## Upgrading

Please see [upgrading] for more information about how to upgrade.

## Security

Please see [security] for more information about how we deal with security.

## Credits

We would like to thank the following contributors for their contributions to this project:

* [All Contributors][all-contributors]

## License

The scripts and documentation in this project are released under the [MIT License][license].

---

<p align="center"><a href="https://van-ons.nl/" target="_blank"><img src="https://opensource.van-ons.nl/files/cow.png" width="50" alt="Logo of Van Ons"></a></p>

[documentation]: docs/README.md#contents
[contributing]: CONTRIBUTING.md
[changelog]: CHANGELOG.md
[upgrading]: UPGRADING.md
[security]: SECURITY.md
[email]: mailto:opensource@van-ons.nl
[all-contributors]: ../../contributors
[license]: LICENSE.md
