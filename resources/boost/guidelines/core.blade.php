## Filament Attachment Library

`van-ons/filament-attachment-library` is the Filament UI layer for `van-ons/laravel-attachment-library`. It ships an `AttachmentField` form component, an "Attachment Library" panel page with a file browser, a `FocalPointPicker` field, and an attachment-browser modal that is auto-registered on every panel page. The core file/storage logic (the `Attachment` model, `HasAttachments` trait, `AttachmentManager` facade, Glide image pipeline, `<x-laravel-attachment-library-image>` Blade component) lives in the upstream `laravel-attachment-library` — guidelines for that package apply here too.

### Setup

@verbatim
<code-snippet name="Install package and assets" lang="bash">
composer require van-ons/filament-attachment-library:^2.0
php artisan filament-attachment-library:install
</code-snippet>

The install command asks whether to install the upstream `van-ons/laravel-attachment-library` (which publishes/runs migrations) and whether to publish Filament assets. Answer yes to both on a fresh install.

<code-snippet name="Register the plugin on a Filament panel" lang="php">
use VanOns\FilamentAttachmentLibrary\FilamentAttachmentLibrary;

public function panel(Panel $panel): Panel
{
    return $panel->plugin(
        FilamentAttachmentLibrary::make()
            ->navigationGroup('Files')              // optional
            ->basePath(fn () => 'tenant-' . tenant()?->id) // optional, string or Closure
    );
}
</code-snippet>
@endverbatim

The plugin registers the `AttachmentLibrary` page automatically. The attachment-browser modal is registered via a `PanelsRenderHook::PAGE_END` hook in the service provider — do not add it manually.

The default disk is the `public` disk; override with `ATTACHMENTS_DISK=…` in `.env`. Use a disk dedicated to attachments to avoid file conflicts.

### Tailwind / theme

Templates use Tailwind. Create a custom Filament theme and add this `@source` line to its `theme.css`:

@verbatim
<code-snippet name="theme.css source path" lang="css">
@source '../../../../vendor/van-ons/filament-attachment-library/resources/**/*.blade.php'
</code-snippet>
@endverbatim

### `AttachmentField` — form component

`VanOns\FilamentAttachmentLibrary\Forms\Components\AttachmentField`. Use in any Filament form schema. Two storage modes: column (default) or relationship (via `HasAttachments` trait).

@verbatim
<code-snippet name="AttachmentField — column storage" lang="php">
use VanOns\FilamentAttachmentLibrary\Forms\Components\AttachmentField;

AttachmentField::make('featured_image')->image();
</code-snippet>

<code-snippet name="AttachmentField — relationship storage" lang="php">
use VanOns\FilamentAttachmentLibrary\Forms\Components\AttachmentField;

// Stored via the HasAttachments morphToMany; pivot row tagged with `collection`.
AttachmentField::make('gallery')
    ->relationship()              // defaults to the `attachments` relation
    ->collection('product_gallery') // optional; defaults to the field name
    ->multiple()
    ->reorderable()               // drag-and-drop ordering, writes `order` to pivot
    ->maxFiles(10)
    ->image();
</code-snippet>
@endverbatim

Public methods on `AttachmentField`:

| Method | Purpose |
|---|---|
| `relationship(string = 'attachments')` | Store via a `MorphToMany` relationship instead of a column. Sets `dehydrated(false)`. |
| `collection(?string)` | Pivot `collection` value when using `relationship()`. Defaults to the field name. |
| `multiple(bool\|Closure = true)` | Allow multi-select. |
| `reorderable(bool\|Closure = true)` | Drag-reorder selected items. Only effective with `multiple()`. Requires the `order` column on `attachables` (shipped with the upstream migrations). |
| `mime(string)` | Filter the picker to a MIME pattern (`'image/png'`, `'image/*'`, …). |
| `image()` / `video()` / `audio()` / `text()` | Shortcut for `mime('image/*')` etc. |
| `minFiles(int)` / `maxFiles(int)` | Wrappers for `minItems` / `maxItems`. |
| `getAttachments()` | Returns an ordered `Collection` of `AttachmentViewModel`s for the current state — use this in custom render code, not the raw IDs. |

The field's state is the attachment ID (or array of IDs when `multiple()`), not file paths. `getState()` returns the first ID for single-select fields and a Collection for multi.

### `FocalPointPicker` — field

`VanOns\FilamentAttachmentLibrary\Filament\Fields\FocalPointPicker`. Lets the user click a point on an image; state shape is `['x' => int, 'y' => int]` (percent). Pair with the upstream `Attachment::$focal_point` cast (json).

@verbatim
<code-snippet name="FocalPointPicker usage" lang="php">
use VanOns\FilamentAttachmentLibrary\Filament\Fields\FocalPointPicker;

FocalPointPicker::make('focal_point')->image($url);
</code-snippet>
@endverbatim

### Configuration

`config/filament-attachment-library.php` keys (publish via `php artisan vendor:publish --tag=filament-attachment-library-config`):

| Key | Default | Purpose |
|---|---|---|
| `user_model` | `Illuminate\Foundation\Auth\User::class` | Resolved when displaying who created/updated an attachment in the browser. |
| `username_property` | `'name'` | Property read off the user model for the display name. |
| `upload_rules` | `[]` | Extra Laravel validation rules merged with the package's built-in upload rules (`AllowedFilename`, `DestinationExists`, `HasValidExtension`). |

Upstream `attachment-library.php` (disk, class mapping, MIME → type mapping) and `glide.php` (image processing) come from `laravel-attachment-library` — see its docs.

### Conventions

- Use `AttachmentField`, not Filament's built-in `FileUpload`, when storing through this package — `FileUpload` won't write `attachments`/`attachables` rows.
- For a column-backed field, the column stores the attachment ID. Cast it accordingly on the model if you want it as `int`.
- For relationship-backed fields, prefer adding a per-collection accessor on the model (`return $this->attachmentCollection('gallery')`) so the rest of the app can read the right slice without re-specifying the collection name.
- Display attachments on the front end with the upstream Blade component, not by hand-rolling URLs:

@verbatim
<code-snippet name="Display an attachment" lang="blade">
<x-laravel-attachment-library-image :src="$attachmentOrId" size="full" aspect-ratio="16/9" />
</code-snippet>
@endverbatim

- The package registers the `attachment-browser` and `attachment-info` Livewire components in `registeringPackage()`. Do not re-register them. The browser modal is rendered by the `PAGE_END` render hook — it is available wherever a Filament panel page is rendered.
- Custom upload validation belongs in `config('filament-attachment-library.upload_rules')`, not on the field — the rules also need to apply to direct uploads from the library page.
- The `AttachmentField` uses `Glide::getSupportedImageFormats()` to populate its helper text — that list comes from `config/glide.php` in the upstream package.
