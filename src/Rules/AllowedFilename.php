<?php

namespace VanOns\FilamentAttachmentLibrary\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use VanOns\LaravelAttachmentLibrary\Exceptions\DisallowedCharacterException;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class AllowedFilename implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $filename = $value;

        if ($value instanceof UploadedFile) {
            $filename = $value->getClientOriginalName();
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $extension = $value->guessExtension() ?? pathinfo($filename, PATHINFO_EXTENSION);
            $filename = "{$name}.{$extension}";
        }

        try {
            AttachmentManager::validateBasename($filename);
        }catch (DisallowedCharacterException $e){
            $fail(__('filament-attachment-library::exceptions.allowed_filename'));;
        }
    }
}
