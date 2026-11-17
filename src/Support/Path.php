<?php

namespace VanOns\FilamentAttachmentLibrary\Support;

use Illuminate\Support\Str;

class Path
{
    /**
     * Drop `.`/`..`/empty segments from a client-supplied path.
     *
     * A backslash separates segments too: the filesystem reads it as a slash, so `..\other` would
     * otherwise pass as one harmless segment and still climb out of the directory.
     */
    public static function sanitize(?string $path): ?string
    {
        $segments = array_filter(
            preg_split('#[/\\\\]#', (string) $path),
            fn (string $segment) => !in_array($segment, ['', '.', '..'], true)
        );

        return $segments === [] ? null : implode('/', $segments);
    }

    /**
     * Whether a path is the base path or lies below it. Without a base path everything is.
     */
    public static function isWithin(?string $path, ?string $basePath): bool
    {
        $basePath = self::sanitize($basePath);

        return $basePath === null || $path === $basePath || self::isBelow($path, $basePath);
    }

    /**
     * Whether a path lies below the base path, so is not the base path itself.
     */
    public static function isBelow(?string $path, ?string $basePath): bool
    {
        $basePath = self::sanitize($basePath);

        if ($path === null) {
            return false;
        }

        return $basePath === null || str_starts_with($path, "{$basePath}/");
    }

    /**
     * A path as seen from the base path: null for the base path itself, unchanged when it lies outside it.
     */
    public static function relativeTo(?string $path, ?string $basePath): ?string
    {
        $basePath = self::sanitize($basePath);

        if ($path === null || $basePath === null) {
            return $path;
        }

        if ($path === $basePath) {
            return null;
        }

        return self::isBelow($path, $basePath) ? substr($path, strlen($basePath) + 1) : $path;
    }

    /**
     * Join the parts of a path, leaving out the ones that are null: the root has no path of its own.
     */
    public static function join(?string ...$parts): ?string
    {
        // Not a bare array_filter(): that would also drop a directory named "0".
        $parts = array_filter($parts, fn (?string $part) => $part !== null);

        return $parts === [] ? null : implode('/', $parts);
    }

    /**
     * The directory a path lies in; null for a path directly in the root.
     */
    public static function parent(string $path): ?string
    {
        return str_contains($path, '/') ? Str::beforeLast($path, '/') : null;
    }

    /**
     * The last segment of a path: the name of the directory or file itself.
     */
    public static function name(string $path): string
    {
        return Str::afterLast($path, '/');
    }
}
