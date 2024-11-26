# Validation

The `AttachmentField` accepts the standard Filament validation rules. Look for the complete list in [Filament's documentation](https://filamentphp.com/docs/3.x/forms/validation).
Furthermore, the package also provides additional validation rules.

## Restricting specific MIME-types

The `AttachmentField` can be restricted to specific MIME-types by calling the `mime` method:

> [!TIP]
> The method also accepts wildcards such as 'image/*'.

```php
AttachmentField::make('featured_image')->mime('image/png')
```

### MIME-specific

The `AttachmentField` provides the ability to filter by different file types by using the following methods:

```php
AttachmentField::make('featured_image')->image()
AttachmentField::make('featured_image')->video()
AttachmentField::make('featured_image')->text()
```

By using these methods, you can increase readability and make it easier to oversee the use of specific file types.

## Multiple attachments

The `AttachmentField` can accept multiple attachments by calling the `multiple` method:

```php
AttachmentField::make('featured_image')->multiple()
```

It is also possible to specify the minimum and maximum amount of allowed files:

```php
AttachmentField::make('featured_image')->minFiles(5)
AttachmentField::make('featured_image')->maxFiles(10)
```
