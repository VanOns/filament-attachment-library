<?php

namespace VanOns\FilamentAttachmentLibrary\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use VanOns\LaravelAttachmentLibrary\DataTransferObjects\Filename;

class HasValidExtension implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $filename = $value instanceof UploadedFile || is_string($value)
            ? new Filename($value)
            : $value;

        if (empty($filename->extension)) {
            $fail(__('filament-attachment-library::validation.invalid_extension', ['attribute' => $attribute]));
        }
    }
}
