<?php

namespace VanOns\FilamentAttachmentLibrary\Support;

use Livewire\Features\SupportFileUploads\FileUploadConfiguration;

class TemporaryUploadLimit
{
    /**
     * Max temporary upload size in bytes, parsed from Livewire's upload rules (null when unlimited).
     */
    public static function bytes(): ?int
    {
        $rules = FileUploadConfiguration::rules();
        $rules = is_string($rules) ? explode('|', $rules) : (array) $rules;

        foreach ($rules as $rule) {
            if (is_string($rule) && preg_match('/^max:(\d+)$/', $rule, $matches)) {
                return (int) $matches[1] * 1024;
            }
        }

        return null;
    }

    public static function label(): ?string
    {
        $bytes = static::bytes();

        return $bytes ? round($bytes / 1048576) . ' MB' : null;
    }
}
