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

On the front end, you can use the laravel-attachment-library-image Blade component to display attachments as images. Image resizing, whether scaling up or down, is managed by Glide. The `src` parameter accepts either an Attachment instance or an ID.

```html
<x-laravel-attachment-library-image :src="$image" />
```