# Changing the directory

Using the Filament plugin, you can change the directory attachments are stored in by using the `directory()` method on the `FilamentAttachmentLibrary` plugin registerer.
You can either pass a string or a closure that returns a string.

For example, you can set it to use current tenant:

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
            ->plugin(
                FilamentAttachmentLibrary::make()
                    ->directory(fn () => 'tenants/' . Filament::getTenant()?->slug)
            );
    }
}
```