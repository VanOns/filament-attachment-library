<?php

namespace VanOns\FilamentAttachmentLibrary\Support;

use enshrined\svgSanitize\Sanitizer;
use Illuminate\Http\UploadedFile;

class SvgUploadSanitizer
{
    /**
     * Strip scripts, event handlers and external references from the file in place if it's
     * an SVG. Returns false if the file claims to be an SVG but couldn't be parsed as one.
     */
    public static function sanitize(UploadedFile $file): bool
    {
        if ($file->getMimeType() !== 'image/svg+xml') {
            return true;
        }

        $sanitizer = new Sanitizer();
        $sanitizer->removeRemoteReferences(true);

        $clean = $sanitizer->sanitize((string) file_get_contents($file->getRealPath()));

        if ($clean === false) {
            return false;
        }

        file_put_contents($file->getRealPath(), $clean);

        return true;
    }
}
