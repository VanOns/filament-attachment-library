<?php

namespace VanOns\FilamentAttachmentLibrary\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class DestinationExists implements ValidationRule
{
    public function __construct(private ?string $path)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $path = "{$this->path}/";

        if ($value instanceof UploadedFile) {
            $path .= $value->getClientOriginalName();
        }

        if (is_string($value)) {
            $path .= $value;
        }

        if (AttachmentManager::destinationExists($path)) {
            $fail('Het opgegeven pad bestaat al.');
        }
    }
}
