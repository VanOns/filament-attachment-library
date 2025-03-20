# Handling attachments

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

Then, add the `AttachmentField` to your resource:

```php
namespace App\Filament\Resources;

use VanOns\FilamentAttachmentLibrary\Forms\Components\AttachmentField;

class ModelNameResource extends Resource
{
    // ...
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                AttachmentField::make('featured_image'),
            ]);
    }
    // ...
}
```

At the front end, the `laravel-attachment-library-image` Blade component can be used to display attachments as image. 
Glide is used to scale the image up or down. The `src` argument may be an Attachment instance, or the id as string/integer.

```html
<x-laravel-attachment-library-image :src="$image" />
```

For more information refer to the [Laravel Attachment Library documentation](https://github.com/VanOns/laravel-attachment-library).
