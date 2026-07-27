<?php

namespace VanOns\FilamentAttachmentLibrary\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFocalPoint implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null) {
            return;
        }

        if (!is_array($value)) {
            $fail('filament-attachment-library::validation.invalid_focal_point')->translate();
            return;
        }

        foreach (['x', 'y'] as $key) {
            $coordinate = $value[$key] ?? null;

            if (!is_numeric($coordinate) || $coordinate < 0 || $coordinate > 100) {
                $fail('filament-attachment-library::validation.invalid_focal_point')->translate();
                return;
            }
        }
    }
}
