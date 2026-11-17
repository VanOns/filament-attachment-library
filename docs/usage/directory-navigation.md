# Directory navigation

The attachment browser can show the directories in two ways. Pick one in `config/filament-attachment-library.php`
(publish it with `php artisan vendor:publish --tag=filament-attachment-library-config`):

```php
'directory_navigation' => 'tree',
```

| Value | What the user sees |
|---|---|
| `cards` (default) | The directories of the current directory as cards above its files. |
| `tree` | A collapsible directory tree beside the files, like a file explorer. Where there is no room for it beside the files, as on a phone, the tree is a block above them that opens with a tap. |

The setting applies to the library page and to the browser an `AttachmentField` opens.

## The directory tree

- Every directory below the [base path](changing-the-base-path.md) is in the tree, with the number of files directly inside it.
  A user never sees or reaches anything above the base path.
- A directory is opened and closed without a request. The open directories and whether the tree itself is collapsed are
  remembered for the session.
- The search field above the tree finds directories at any depth. The search field above the files then only finds files.
- The menu on a row creates a subdirectory, or renames or deletes the directory.

> [!NOTE]
> The tree reads every directory below the base path from the disk on each update of the browser. On a local disk this
> is quick; on a remote disk with a very large number of directories the cards are the better choice.

## Moving files by dragging

In both navigations a file can be dragged onto a directory to move it there: onto a row of the tree, or onto a directory
card. Dragging a selected file takes the whole selection along. The notification that follows can undo the move.

## Sorting

Directories are always listed from A to Z. Sorting by name ignores case, for directories and for files.
