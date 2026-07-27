<?php

namespace VanOns\FilamentAttachmentLibrary\Support;

class Path
{
    /**
     * Drop `.`/`..`/empty segments from a client-supplied path.
     */
    public static function sanitize(?string $path): ?string
    {
        $segments = array_filter(
            explode('/', (string) $path),
            fn (string $segment) => !in_array($segment, ['', '.', '..'], true)
        );

        return $segments === [] ? null : implode('/', $segments);
    }
}
