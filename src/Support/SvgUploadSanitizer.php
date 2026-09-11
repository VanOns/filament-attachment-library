<?php

namespace VanOns\FilamentAttachmentLibrary\Support;

use enshrined\svgSanitize\Sanitizer;
use Illuminate\Http\UploadedFile;

class SvgUploadSanitizer
{
    /**
     * Strip scripts, event handlers and external references from the file if it's an SVG,
     * returning the sanitized file to use in its place. Non-SVG files are returned as-is.
     * Returns null if the file claims to be an SVG but couldn't be parsed as one.
     */
    public static function sanitize(UploadedFile $file): ?UploadedFile
    {
        if (
            $file->getMimeType() !== 'image/svg+xml'
            && strtolower($file->getClientOriginalExtension()) !== 'svg'
        ) {
            return $file;
        }

        $sanitizer = new Sanitizer();
        $sanitizer->removeRemoteReferences(true);

        $clean = $sanitizer->sanitize((string) $file->get());

        if ($clean === false) {
            return null;
        }

        $path = tempnam(sys_get_temp_dir(), 'svg');

        if ($path === false || file_put_contents($path, $clean) === false) {
            return null;
        }

        register_shutdown_function(static fn () => @unlink($path));

        return new UploadedFile(
            $path,
            $file->getClientOriginalName(),
            $file->getMimeType(),
        );
    }
}
