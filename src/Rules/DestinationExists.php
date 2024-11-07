<?php

namespace VanOns\FilamentAttachmentLibrary\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use VanOns\LaravelAttachmentLibrary\DataTransferObjects\Filename;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class DestinationExists implements ValidationRule
{
    public function __construct(private ?string $path, private ?int $attachmentId = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /**
         * @var Attachment $attachment
         */
        $attachment = Attachment::find($this->attachmentId);

        $path = "{$this->path}/";

        if ($value instanceof UploadedFile) {
            $path .= new Filename($value);
        }

        if (is_string($value) && $this->attachmentId !== null) {
            $extension = $attachment->extension;
            $path .= implode('.', array_filter([$value, $extension]));
        }

        if (is_string($value) && $this->attachmentId === null) {
            $path .= $value;
        }

        if (AttachmentManager::destinationExists($path)) {
            $fail('filament-attachment-library::validation.destination_exists')->translate();
        }
    }
}
