# Handling attachments

Firstly, add the `HasAttachments` trait to your desired model.

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

Then add the `AttachmentField` to your resource.

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