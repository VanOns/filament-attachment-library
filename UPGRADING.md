# Upgrading

We aim to make upgrading between versions as smooth as possible, but sometimes it involves specific steps to be taken.
This document will outline those steps. And as much as we try to cover all cases, we might miss some. If you come
across such a case, please let us know by [opening an issue][issues], or by adding it yourself and creating a pull request.

# v0 to v1
* Remove the `HandlesFormAttachments` trait from your Edit and Create Filament Pages.
* Add `->relationship()->collection(null)` to your AttachmentField definitions if you store attachments in the `attachments` relationship of your model.
* Run `php artisan filament-attachment-library:install` to publish new migrations.
* Run `php artisan migrate` to update the database.

<!-- EXAMPLE -->
<!--
# v1 to v2

* Remove the `foo` column from the `bar` table.
* Add the `baz` column to the `bar` table.
* Run `php artisan migrate` to update the database.
-->

[issues]: https://github.com/VanOns/filament-attachment-library/issues
