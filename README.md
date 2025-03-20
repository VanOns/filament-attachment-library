<p align="center"><img src="art/social-card.png" alt="Social card of Filament attachment library"></p>

# Filament Attachment Library

<!-- BADGES -->

Filament package for easy attachment uploading and browsing.

## Quick start

### Installation

The Filament Attachment Library can be installed using Composer by running the following command:

```bash
$ composer require van-ons/filament-attachment-library
```

An installation command is available that ensures that the migrations and assets are installed:

```bash
$ php artisan filament-attachment-library:install
```

The templates in this package use TailwindCSS. To ensure the styling is rendered correctly, a custom Filament
theme must be set up, and the `tailwind.config.js` file should be extended.

Create the custom Filament theme and follow the instructions in the terminal to set it up:

```bash
php artisan make:filament-theme [PANEL_NAME]
```

Add the following to the `tailwind.config.js` file:

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

Then, register the plugin in the desired Filament panel:

```php
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

First, add the `HasAttachments` trait to your desired model:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use VanOns\LaravelAttachmentLibrary\Concerns\HasAttachments;

class ModelName extends Model
{
    use HasAttachments;

    // ...
}
```

Then, in your form schema, add the `AttachmentField`:

```php
namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use VanOns\FilamentAttachmentLibrary\Forms\Components\AttachmentField;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            // ...
            AttachmentField::make('attachments'),
        ]);
}
```

Import the `HandlesFormAttachments` trait in your Filament resource `create` and `edit` pages:

```php
namespace App\Filament\Resources\ModelResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use VanOns\FilamentAttachmentLibrary\Forms\Traits\HandlesFormAttachments;

class CreateModel extends CreateRecord
{
    use HandlesFormAttachments;
}
```

Note: If you plan to overwrite the `handleRecordCreation()`, `handleRecordUpdate()`,
or `mutateFormDataBeforeFill()` methods, please check out the trait's code and
re-use the `retrieveAttachments()` and `syncAttachments()` methods.

Finally, at the front end, the `laravel-attachment-library-image` Blade component can be used to display attachments as image. 
Glide is used to scale the image up or down. The `src` argument may be an Attachment instance, or the id as string/integer.

```html
<x-laravel-attachment-library-image :src="$image" />
```

For more information refer to the [Laravel Attachment Library documentation](https://github.com/VanOns/laravel-attachment-library).

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
