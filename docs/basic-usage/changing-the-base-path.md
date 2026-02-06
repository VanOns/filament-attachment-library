# Changing the base path

When registering the Filament plugin, you can change the path attachments are stored in by using the `basePath()` method on the `FilamentAttachmentLibrary` instance.
You can either pass a string or a closure that returns a string.

For example, you can set it to use the current tenant's slug:

```php
<?php

namespace App\Providers\Filament;

use VanOns\FilamentAttachmentLibrary\FilamentAttachmentLibrary;
use Filament\Facades\Filament;

class ExamplePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->tenant(...)
            ->plugin(
                FilamentAttachmentLibrary::make()
                    ->basePath(fn () => 'tenants/' . Filament::getTenant()?->slug)
            );
    }
}
```