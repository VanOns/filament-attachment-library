---
name: attachment-field
description: Add a Filament Attachment Library AttachmentField to a Filament form (resource, page, or relation manager). Use when the user wants to attach files/images/PDFs/videos to a model through a Filament form — picking from the attachment library rather than uploading directly via Filament's FileUpload. Covers column-backed vs relationship-backed storage, MIME filtering, multi-select, reordering, focal points, and front-end display.
---

# Add an AttachmentField to a Filament form

## When to use this skill

Use this when the user asks to:

- Add an attachment / image / file / gallery field to a Filament resource, page, or relation manager.
- Let users pick from the attachment library instead of uploading inline.
- Show a focal-point picker for a stored image.
- Display an attachment on the front end (Glide-backed responsive image).

Do **not** use this skill for:

- The initial package install (the `filament-attachment-library:install` command and panel-plugin registration are covered by the always-loaded guidelines).
- Custom file processing (`AttachmentManager` / `Glide` workflows belong to the upstream `laravel-attachment-library`).

## Step 1 — Pick a storage mode

| You want… | Mode | What to do |
|---|---|---|
| One attachment ID stored on the model itself | **Column** | Add a column (e.g. `featured_image_id`) on the model's table. No trait needed. |
| Multiple attachments, or attachments grouped by purpose ("gallery", "downloads", …) | **Relationship** | Add the `HasAttachments` trait. The pivot's `collection` column distinguishes groups. |

Both modes can be mixed on the same model.

## Step 2 — Prepare the model

### Column mode

Make sure the column exists and is fillable. The field stores the attachment's primary key:

```php
// migration
$table->foreignId('featured_image_id')->nullable()->constrained('attachments')->nullOnDelete();
```

```php
// App\Models\Article
protected $fillable = ['featured_image_id', /* … */];
protected $casts = ['featured_image_id' => 'integer'];
```

### Relationship mode

Add the trait. The migrations from the upstream package already create `attachments` and `attachables` (with `collection` and `order` columns):

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use VanOns\LaravelAttachmentLibrary\Concerns\HasAttachments;

class Article extends Model
{
    use HasAttachments;

    public function gallery(): MorphToMany
    {
        return $this->attachmentCollection('gallery');
    }
}
```

The `gallery()` accessor is optional but recommended — it lets the rest of the app read the right slice without re-specifying the collection name.

## Step 3 — Add the field to the form

```php
use VanOns\FilamentAttachmentLibrary\Forms\Components\AttachmentField;

public static function form(Form $form): Form
{
    return $form->schema([
        // Column-backed
        AttachmentField::make('featured_image_id')
            ->image()
            ->required(),

        // Relationship-backed gallery
        AttachmentField::make('gallery')
            ->relationship()           // uses the `attachments` morph relation
            ->collection('gallery')    // pivot tag; defaults to field name
            ->multiple()
            ->reorderable()
            ->image()
            ->maxFiles(20),
    ]);
}
```

### Field options

| Method | Default | Notes |
|---|---|---|
| `relationship(string = 'attachments')` | column mode | Switches to relationship mode and disables dehydration. |
| `collection(?string)` | field name (when `relationship()` is called) | Pivot `collection` value. Set this if you want the field name and collection to differ. |
| `multiple(bool\|Closure = true)` | `false` | Multi-select. |
| `reorderable(bool\|Closure = true)` | `true` (only effective with `multiple()`) | Drag-and-drop ordering. Persists to the pivot's `order` column. |
| `mime(string)` | none | MIME filter for the picker. Wildcards allowed (`'image/*'`). |
| `image()` / `video()` / `audio()` / `text()` | — | Shortcut for `mime('image/*')` etc. |
| `minFiles(int)` / `maxFiles(int)` | unbounded | Wrappers for `minItems` / `maxItems` from `CanLimitItemsLength`. |
| Standard Filament rules (`required`, `nullable`, …) | — | Work as usual. See [Filament validation docs](https://filamentphp.com/docs/forms/validation). |

### Decision: column or relationship?

```
Single attachment, lives only on this model? ─ column
Multiple attachments?                        ─ relationship + multiple()
Need to reuse the same attachment across models? ─ relationship (column would duplicate IDs)
Need ordering? ─ relationship + multiple() + reorderable()
```

## Step 4 — Display attachments

Use the Blade component shipped by the upstream package — never hand-roll URLs (Glide URLs are signed):

```blade
{{-- Column-backed: $article->featured_image_id is an int --}}
<x-laravel-attachment-library-image
    :src="$article->featured_image_id"
    size="full"
    aspect-ratio="16/9"
/>

{{-- Relationship-backed: iterate the relation --}}
@foreach ($article->gallery as $image)
    <x-laravel-attachment-library-image :src="$image" size="medium" />
@endforeach
```

The component accepts an `Attachment` instance, an integer ID, or a string filename.

## Step 5 (optional) — Focal point picker

If a column-backed image needs a focal point, use the upstream `focal_point` JSON column and the `FocalPointPicker` field:

```php
use VanOns\FilamentAttachmentLibrary\Filament\Fields\FocalPointPicker;

FocalPointPicker::make('focal_point')
    ->image(fn ($get) => $get('featured_image_id'));
```

State shape: `['x' => 0–100, 'y' => 0–100]` (percent). Persist to a `json` column on the model. The `Attachment` model already has a `focal_point` json cast — use that if you're storing the focal point on the attachment itself rather than per-usage.

## Common follow-ups

- **Filtering attachments by MIME on the picker**: `->mime('image/png')` or one of the shortcuts. The picker also exposes a built-in MIME filter UI; `->mime()` *restricts* the picker further.
- **Custom upload validation**: add rules to `config/filament-attachment-library.php` `upload_rules`, not on the field. The library page uploads files outside the form's validation context.
- **Per-tenant base path**: `FilamentAttachmentLibrary::make()->basePath(fn () => 'tenants/' . tenant()->slug)` on the panel — see the always-loaded guidelines.
- **Custom user display in the browser**: set `user_model` and `username_property` in `config/filament-attachment-library.php`.

## Anti-patterns

- ❌ Using Filament's `FileUpload` to store attachments — bypasses the library, no `attachments` rows are created.
- ❌ Calling `relationship()` without the `HasAttachments` trait on the model — the field can't load existing state.
- ❌ Using `reorderable()` without `multiple()` — has no effect; reordering only applies when there are multiple selected items.
- ❌ Setting the `collection` to a value that overlaps with another field's collection on the same model — they will share the same pivot rows.
- ❌ Hand-constructing `/storage/...` or Glide URLs — go through `<x-laravel-attachment-library-image>` or `$attachment->url`.
- ❌ Assuming `getState()` returns IDs in the original order without `reorderable()` — without the `order` pivot column being maintained, ordering is not stable.
