<?php

namespace VanOns\FilamentAttachmentLibrary\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Checks an upload against a field's mime pattern and allowed extensions. Uses the
 * server-detected mime type, since the browser-supplied type is bypassable.
 */
class MatchesFileFilter implements ValidationRule
{
    /**
     * @param  array<int, string>  $extensions  Lowercase extensions; empty allows every extension.
     */
    public function __construct(
        protected ?string $mime = null,
        protected array $extensions = [],
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            return;
        }

        $matchesMime = !$this->mime || Str::is($this->mime, (string) $value->getMimeType());
        $matchesExtension = $this->extensions === [] || in_array(strtolower($value->getClientOriginalExtension()), $this->extensions);

        if (!$matchesMime || !$matchesExtension) {
            $fail(__('filament-attachment-library::notifications.attachment.upload_failed_wrong_type'));
        }
    }
}
