# Upgrading

We aim to make upgrading between versions as smooth as possible, but sometimes it involves specific steps to be taken.
This document will outline those steps. And as much as we try to cover all cases, we might miss some. If you come
across such a case, please let us know by [opening an issue][issues], or by adding it yourself and creating a pull request.

# v0 to v1
* Remove the `HandlesFormAttachments` trait from your Edit and Create Filament Pages.
* Add `->relationship()->collection(null)` to your AttachmentField definitions if you store attachments in the `attachments` relationship of your model.
    * Note: You can change `null` to any collection name you want to use. But this means that you have to update the `collection` column in your `attachables` table to reflect this. 
* Run `php artisan filament-attachment-library:install` to publish new migrations.
* Run `php artisan migrate` to update the database.

# v2.6 to v2.7

* Require `van-ons/laravel-attachment-library` `^1.7` and follow its [upgrade notes](https://github.com/VanOns/laravel-attachment-library/blob/main/UPGRADING.md):
  publish and run its new migrations for video dimensions, posters and captions.
* Run `php artisan filament:assets` to publish the new stylesheet.
* Rebuild your panel theme, so the classes used by the new views are included.
* Optionally install `ffmpeg` and `ffprobe` on the server to store video dimensions and generate posters.
* The attachment browser's Livewire events are now scoped per browser (e.g. `highlight-attachment.{scope}`).
  Update any custom code that dispatches `highlight-attachment`, `dehighlight-attachment` or `mount-action` itself.

<!-- EXAMPLE -->
<!--
# v1 to v2

* Remove the `foo` column from the `bar` table.
* Add the `baz` column to the `bar` table.
* Run `php artisan migrate` to update the database.
-->

[issues]: https://github.com/VanOns/filament-attachment-library/issues
