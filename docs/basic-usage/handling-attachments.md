# Handling attachments

The attachment field can be used in two ways: either to store the attachments in a specific column of your model,
or to store the attachments in the `attachments` relationship by using the `HasAttachments` trait and the `relationship()` method.

(Optional) Add the `HasAttachments` trait to your desired model:

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

This will add the `attachments()` relationship which links one or more
attachments to your model. By default the attachments will be stored in a collection that matches the name of the field (e.g. `gallery`). You can provide a different collection name as the first parameter of the `relationship` method. Or you can force the collection to be `null` by calling `collection(null)`.

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
            // If you want to store the attachments in a column
            AttachmentField::make('featured_image'),
            // Or if you want to store attachments in the attachments relationship with a specific collection name
            AttachmentField::make('gallery')->relationship(),
            // Or if you want the collection name to be different from the field name
            AttachmentField::make('gallery')->relationship()->collection('product_gallery'),
        ]);
}
```

(Optional) When using the `relationship()` method, you can filter the attachments by collection name. To make this easier you can add a separate relationship method to your model:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use VanOns\LaravelAttachmentLibrary\Concerns\HasAttachments;

class ModelName extends Model
{
    use HasAttachments;

    public function gallery(): MorphToMany
    {
        return $this->attachmentCollection('gallery');
    }
}
```

Finally, at the front end, the `laravel-attachment-library-image` Blade component can be used to display attachments as image.
Glide is used to scale the image up or down. The `src` argument may be an Attachment instance, or the id as string/integer.

```html
<x-laravel-attachment-library-image :src="$image" />
```

For more information refer to the [Laravel Attachment Library documentation](https://github.com/VanOns/laravel-attachment-library).
