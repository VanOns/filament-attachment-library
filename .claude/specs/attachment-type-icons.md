# Type icons for attachments

## Goal

Make file types recognisable at a glance in the library. A video with a poster must not look like its poster image, and subtitle files get an icon of their own.

## Non-goals

- Overlays on thumbnails, such as a play button on posters.
- New file types or changes to how types are detected for images and videos.

## Context & assumptions

- Grid and list tiles show the poster thumbnail for a video (`AttachmentViewModel::posterUrl()`), so a video tile looks like its poster's tile. Only the subtitle (`MP4 — 0:02 — size`) differs.
- Type icons are chosen by hand in six views, and inconsistently (`film` and `video-camera`; `document` and `document-text`):
  - grid tile
  - list tile
  - input-layout row
  - info sidebar
  - edit header
  - edit file preview
- `items.grid-item` and `items.list-item` are shared with directory tiles.
- Heroicons has no captions icon. `chat-bubble-bottom-center-text` is the closest.

## Requirements

1. `AttachmentViewModel::icon(): string` is the only place that maps a type to an icon:
   - images → `heroicon-o-photo`
   - videos → `heroicon-o-video-camera`
   - subtitles (`.vtt` / `.srt`, checked case-insensitively on the extension) → `heroicon-o-chat-bubble-bottom-center-text`
   - everything else → `heroicon-o-document-text`

   `AttachmentViewModel::isSubtitle()` exposes the subtitle check.
2. The grid tile, list tile, input-layout row, info sidebar, edit header and edit file preview all use `icon()` instead of their own choice. The input row's empty state keeps `folder-open`.
3. Grid and list tiles show `icon()` as a small icon before their subtitle line, for every file type. The info sidebar's summary line under the file name does the same. Directory tiles are unchanged.
4. A video with a poster still shows the poster as its thumbnail.

## Data model

None.

## Behaviour & edge cases

- A subtitle stored as `text/plain` still gets the subtitle icon, because the check uses the extension.
- In the edit header, subtitles keep the gray "File" badge; only the icon changes.

## Packages vs. custom work

Custom, inside this package, using Heroicons through `x-filament::icon`.

## Testing

`composer analyse` and `composer format:check`, plus a browser check in the demo app:
- grid and list with a video, its poster, a `.vtt`, an `.srt`, a text file and a directory
- the info sidebar for each
- the edit header and file preview of a subtitle file
- caption input rows

## Done when

All requirements are met, the checks pass, and the video and poster tiles are visibly different.

## Open decisions

None.
