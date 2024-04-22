<?php

namespace VanOns\FilamentAttachmentLibrary\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LimitSelectedAttachments implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (count($value) === 1) {
            return;
        }

        $fail('The :attribute is invalid.');
    }
}
