# AttachmentField: Input layout

## Goal

Give `AttachmentField` a third way to show its selection: a small field that looks and behaves like a Filament input. It shows the file's thumbnail or icon, its name and its type, and is clicked to pick or change the file. The edit slide-over uses it for the poster and caption fields.

## Non-goals

- Changing the default layout or how the existing Grid and List layouts look.
- Changing the attachment browser modal itself.
- Chip-style multi-select inside one input.

## Context & assumptions

- `AttachmentField` (`src/Forms/Components/AttachmentField.php`) has `compact(bool)`. The selection is rendered by `resources/views/components/items/field.blade.php`:
  - grid cards by default
  - `attachment.list-item` rows when compact
- The field view (`resources/views/forms/components/attachment-field.blade.php`) always renders a "Choose files" button below the selection.
- An empty field renders a dashed "No file has been selected" box (`forms.attachment_field.no_file_selected`).
- The field's Alpine component `attachmentField` (`resources/js/plugin.js`) already handles:
  - `openBrowser(highlight)`
  - `attachment-removed` and `attachment-reordered`
  - drop uploads through the nested `attachment-field-uploader`
  - `maxItems` limits
- `attachmentSortable` handles reordering through `[data-drag-handle]` / `[data-attachment-id]`.
- `AttachmentViewModel` provides `extension` (uppercase), `thumbnailUrl()`, `posterUrl()`, `isImage()` and `isVideo()`.
- `src/Enums/Layout.php` (Grid/List) belongs to the browser. The field gets its own enum so browser and field layouts stay independent.
- The plugin's Blade views are scanned by the panel theme's Tailwind, so new utility classes must appear literally in Blade files.

## Requirements

1. A new enum `VanOns\FilamentAttachmentLibrary\Enums\AttachmentFieldLayout` with the cases `GRID`, `LIST` and `INPUT`, in UPPERCASE like the existing `Layout` enum.
2. `AttachmentField::layout(AttachmentFieldLayout|Closure $layout)` and `getLayout(): AttachmentFieldLayout`. The default is `Grid`.
3. `compact(bool|Closure $condition = true)` stays as a shortcut: `true` sets `LIST`, `false` sets `GRID`. `getCompact()` stays for backwards compatibility and returns whether the layout is `LIST`. Internal code uses `getLayout()`.
4. In the Input layout, each item is rendered inside a Filament input wrapper (`x-filament::input.wrapper`), so it gets the same height, ring, focus, `disabled` and `valid`/error states as other inputs.
   - Clicking anywhere on the item opens the browser. For an item that holds a file, `highlight` is set to that file.
5. A filled item shows, from left to right:
   - a drag handle, only when the field is reorderable (`multiple` and `reorderable`)
   - a 20×20 rounded thumbnail for images, or the poster thumbnail for videos that have one, or else a type icon (photo, video-camera or document-text)
   - the truncated file name, with the full filename in the `title`
   - a gray extension badge (`x-filament::badge size="sm"`)
   - a × button that removes the file (`attachment-removed`), hidden when the field is disabled
6. An empty item, or the "add" row, shows a folder icon and the placeholder `forms.attachment_field.choose` ("Choose a file…"; nl: "Kies een bestand…").
   - With `multiple()` it shows `forms.attachment_field.add` ("Add a file…"; nl: "Bestand toevoegen…").
7. In the Input layout the field view does not render the separate "Choose files" button.
8. `multiple()` in the Input layout:
   - one input row per selected file, stacked with `gap-2`
   - an add row at the bottom, hidden once `maxItems` is reached
   - reordering by the drag handle uses the existing `attachmentSortable`
9. Drag-and-drop upload works in the Input layout through the existing drop overlay, which covers the rows.
10. A single field with a selected file shows only that file's row, with no add row. Clicking it replaces the file.
11. The edit slide-over uses `->layout(AttachmentFieldLayout::Input)` for `poster_id` and for the caption `caption_id`, replacing `->compact()`.
12. Documentation:
    - `docs/usage` gets a "Layouts" section covering Grid, List and Input and the `compact()` shortcut.
    - The boost skill's options table gets `layout()`.
    - `CHANGELOG.md` gets an entry.

## Data model

None.

## Behaviour & edge cases

- **Disabled field:** the wrapper is disabled, clicks do nothing, and there is no × and no add row.
- **File with no thumbnail** (SVG not supported by Glide, video without a poster, or any document): the type icon is shown.
- **Long names:** truncated with an ellipsis. The badge and × always stay visible.
- **Validation error on the field:** the wrapper shows Filament's invalid state (`:valid="! $errors->has($statePath)"`).
- **Narrow table cells** (caption repeater): the row shrinks. The name truncates first, then the badge stays.

## Packages vs. custom work

Custom, inside this package. It reuses Filament's `input.wrapper`, `badge` and `icon` components and the existing Alpine components.

## Testing

- `composer analyse` and `composer format:check`; `npm run lint` and `npm run build` if the JS changes.
- Manual check in `filament-cms-demo`:
  - a single field empty and filled (image, video with poster, VTT)
  - change and clear
  - `multiple()` with reorder, remove, add and the `maxItems` limit
  - disabled
  - validation error
  - drop upload
  - the poster and caption fields in the edit slide-over
  - dark mode

## Done when

- Every requirement is implemented, and the Grid and List layouts look unchanged.
- The edit slide-over's poster and caption fields look like compact inputs, with no "Choose files" buttons.
- The checks pass and the docs are updated.

## Open decisions

None.
