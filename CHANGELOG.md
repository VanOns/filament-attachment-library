# Changelog

All changes to this project will be documented in this file.

## Unreleased

### Added

- Video poster images and caption tracks in the edit action, and poster thumbnails, duration and captions in the library.
- `AttachmentField::extensions()` to restrict picking and uploading by file extension.
- `AttachmentField::layout()` with a new input layout (`AttachmentFieldLayout::INPUT`); `compact()` is now a shortcut for the list layout.
- Nested attachment browser modals: a picker opened inside a browser modal opens a new level instead of replacing it.

### Changed

- Redesigned the edit slide-over: a preview column (focal point with crop previews, video player, text file preview) next to Details, Accessibility and Captions sections, with Replace, Move and Delete in its header.

## v0.0.3 - 2024-11-08

### What's changed

- fix: Return model after synching #16 (by @ptrcksc)
