# Video improvements (VOOS-120)

Jira: https://vanons.atlassian.net/browse/VOOS-120

## Goal

Improve video support in the attachment library with:

- poster images, including a first frame made by ffmpeg when it is available
- video width, height and duration exposed to the frontend, so layouts don't shift (CLS)
- caption tracks (WebVTT, and SRT converted to WebVTT) for WCAG compliance

## Non-goals

- A captions editor. That will be a separate follow-up ticket.
- Queued or background processing. Probing and poster generation run synchronously.
- Probing videos or generating posters during auto-sync (`updateFiles`).
- Deleting posters or caption files when their video is deleted.
- Transcoding, thumbnails at other timestamps, or HLS/adaptive streaming.

## Context & assumptions

- Core logic goes in `van-ons/laravel-attachment-library` (`../laravel-attachment-library`). The Filament plugin only adds UI (see CLAUDE.md).
- Metadata currently comes from `MetadataAdapter` subclasses mapped to MIME types in `attachment-library.metadata_retrievers`. Results are cached for a day and nothing is stored in the database. `FileMetadata` already has a `videoDuration` field, but nothing fills it.
- No code uses ffmpeg yet. The server may not have it installed, so every ffmpeg feature must fail quietly when it's missing.
- Auto-sync (`AttachmentManager::updateFiles`) creates an `Attachment` row for every file on disk. Generated posters and `.vtt` files therefore have to be ordinary attachments; hidden sidecar files would be picked up anyway.
- `updateFiles` uses a bulk `insert()`, so model events don't fire there. That's consistent with the "no probing during auto-sync" non-goal.
- Browsers' `<track>` element only plays WebVTT, so SRT files are converted.
- `EditAttachmentAction` already shows type-specific sections (focal point for images), and `AttachmentField` can be used as a picker inside actions.
- The library has PHPUnit tests. The Filament plugin has none.

## Requirements

### Library: data & processing

1. A migration stub `add_video_fields_to_attachments_table` adds nullable `width` (unsigned int), `height` (unsigned int), `duration` (float, in seconds) and `poster_id` to `attachments`. `poster_id` is a FK to `attachments.id` with `nullOnDelete`.
2. A migration stub `create_attachment_captions_table` creates `attachment_captions` with these columns:
   - `id`
   - `video_id` (FK to attachments, `cascadeOnDelete`)
   - `caption_id` (FK to attachments, `cascadeOnDelete`)
   - `language` (string, BCP 47, e.g. `nl`)
   - `label` (nullable string)
   - `is_default` (bool, default false)
   - `order` (unsigned int, default 0)
   - timestamps
3. Config `attachment-library.ffmpeg` has `ffmpeg_path` (default `env('FFMPEG_PATH', 'ffmpeg')`), `ffprobe_path` (default `env('FFPROBE_PATH', 'ffprobe')`) and `timeout` (default 60 seconds).
4. An `Ffmpeg` support class uses Laravel's `Process` facade and provides:
   - `isAvailable()`: true when both binaries run successfully. Memoised per request.
   - `probe(Attachment): ?array`: returns width, height and duration.
   - `extractFirstFrame(Attachment, string $targetPath)`: writes a JPEG.

   Videos on remote disks are downloaded to a temp file first, the same way `getImageSizes` already does it.
5. A new `Ffprobe` metadata adapter fills `FileMetadata` width, height and `videoDuration` for `video/*` and is registered by default in `metadata_retrievers`. It returns `false` when ffmpeg is unavailable.
6. When a video is uploaded (`AttachmentManager::upload`) or replaced (`replace`) and ffmpeg is available:
   - its width, height and duration are probed and saved to the columns
   - if it has no poster, the first frame is saved as `<video name>-poster.jpg` in the same directory, using the usual unique-name logic if that name is taken, and linked through `poster_id`

   When ffmpeg is unavailable, both steps are skipped and the upload still succeeds.
7. `AttachmentManager::generatePoster(Attachment $video): Attachment` does the first-frame extraction on demand. It replaces the existing `poster_id` link but doesn't delete the old poster attachment.
8. A pure-PHP `SrtToVtt` converter turns SRT content into valid WebVTT:
   - adds a `WEBVTT` header
   - changes `,` to `.` in timestamps
   - strips BOM and normalises CRLF
9. Linking an `.srt` caption creates a `.vtt` attachment with the same name next to it (unique name if taken) and links that. The original `.srt` stays in the library unchanged.
10. `Attachment` gains:
    - `poster(): BelongsTo`
    - `captions()`: the linked caption records, ordered by `order`, with language, label and is_default available
    - `aspectRatio` accessor (`width / height`, or null)
    - fillable and casts for the new columns
11. `AttachmentResource` adds:
    - `width`, `height`, `duration`, `aspect_ratio`
    - `poster` (nested `AttachmentResource` or null)
    - `captions` (list of `{url, language, label, is_default}`)
12. A `<x-laravel-attachment-library-video>` Blade component (registered like the image component) accepts `src` (id, filename or Attachment) and passes other attributes through. It renders:
    - `<video>` with `width`/`height` and `style="aspect-ratio: w / h"` when known
    - the `poster` attribute as a Glide URL of the poster
    - a `<source>` with its MIME type
    - one `<track kind="captions" src srclang label [default]>` per caption
13. The artisan command `attachment-library:process-videos {--posters} {--force}` probes every video without dimensions (all videos with `--force`). With `--posters` it also creates missing posters. It shows a progress bar, and when ffmpeg is unavailable it exits non-zero with a clear message.

### Filament plugin: UI

14. For videos, `EditAttachmentAction` shows a "Video" section with:
    - a poster picker (`AttachmentField`, image only, single)
    - a "Generate from first frame" action that calls `generatePoster` and refreshes the picker, hidden when ffmpeg is unavailable
    - a captions `Repeater` with: caption file (`AttachmentField` limited to `.vtt`/`.srt`, required), language (required, max 35), label (optional) and default (toggle, at most one per video)

    Saving syncs the `attachment_captions` rows, converting SRT links as described in requirement 9.
15. The `alt` and `caption` text fields stay image-only. Captions are files, not the `caption` column.
16. For videos, the `AttachmentInfo` sidebar shows dimensions (`WxH`), duration (`m:ss`) and a poster thumbnail. The `<video>` preview in the sidebar uses the poster.
17. Grid and list items for videos use the poster image as their thumbnail when one is set.
18. All new strings get translations under `filament-attachment-library::` in both `en` and `nl`.
19. The attachment browser modal supports nesting to any depth. Opening a picker while a browser modal is open opens a new browser level on top. Each level has its own Livewire instance and state (path, selection, filters), and closing a level leaves the levels below it untouched. Highlight and info events are scoped per browser instance.
20. `AttachmentField::extensions(array)` restricts both picking and uploading by file extension, case-insensitively. It is applied in the browser query, in drop and upload validation (server and client) and in the field uploader. The caption picker uses `extensions(['vtt', 'srt'])`.

### Library: naming

21. Generated files (posters and converted VTT) never overwrite an existing file. `AttachmentManager::uniqueFilename()` appends `-1`, `-2`, … until the name is free. Regular `upload()` keeps throwing `DestinationAlreadyExistsException`.

## Data model

```
attachments
  + width      unsigned int  null
  + height     unsigned int  null
  + duration   float         null   -- seconds
  + poster_id  FK attachments.id null, nullOnDelete

attachment_captions
  id, video_id → attachments (cascade), caption_id → attachments (cascade),
  language string, label string null, is_default bool, order uint, timestamps
```

## Behaviour & edge cases

- **ffmpeg missing:** uploads work as before, dimensions stay null, no poster is generated, the Generate button is hidden and the command fails with a message. `<x-video>` leaves out width, height and poster when they're null.
- **Probe or extract fails** (corrupt file, unsupported codec, non-zero exit or timeout): log a warning, leave the fields null and don't throw to the uploader.
- **Very short or audio-only "video":** if no video stream is found, dimensions stay null and no poster is made.
- **Replace:** dimensions are re-probed. An existing poster is kept. A poster is only generated when none is linked.
- **Rename or move a video:** the poster and captions stay linked by ID, so nothing else needs to change.
- **Poster or caption attachment deleted:** `poster_id` becomes null and the caption row cascades away.
- **Video deleted:** its caption rows are deleted. The poster and caption files stay in the library.
- **SRT to VTT name clash:** if the `.vtt` name is taken, the existing unique-name logic picks another name. An existing `.vtt` is never overwritten.
- **Multiple defaults:** validation allows at most one `is_default` per video.
- **Same language twice:** allowed (e.g. "English" and "English (SDH)"); the label tells them apart.

## Packages vs. custom work

- This is our own in-house media package, so all the work goes here and in `laravel-attachment-library`.
- ffmpeg/ffprobe are called through Laravel's `Process` facade. No `php-ffmpeg` dependency.
- SRT to VTT conversion is custom, small and pure PHP.

## Testing

- PHPUnit in `laravel-attachment-library`:
  - SRT to VTT converter (timestamps, BOM, CRLF, multi-line cues)
  - parsing ffprobe JSON into width, height and duration (with `Process::fake()`)
  - upload flow with ffmpeg faked as available and as unavailable
  - `poster`/`captions` relations and the `aspectRatio` accessor
  - `AttachmentResource` output
  - Video Blade component rendering
  - `process-videos` command
- Filament plugin: `composer analyse` and `composer format:check` pass. Test by hand in testbench: edit a video, pick or generate a poster, add VTT and SRT captions, check the sidebar and the grid thumbnail.

## Done when

- All requirements above are implemented in both packages, and the migrations are publishable.
- Library tests pass, and PHPStan and php-cs-fixer pass in both packages.
- A video uploaded on a machine with ffmpeg gets dimensions, duration and a poster automatically. Without ffmpeg the upload still works.
- `<x-video>` renders a CLS-safe `<video>` with a poster and caption tracks.
- READMEs and docs for both packages, and the boost skill/guidelines, describe the ffmpeg requirement, the config, the command and the component.

## Open decisions

- Captions editor: deferred to a follow-up ticket.
