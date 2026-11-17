<?php

namespace VanOns\FilamentAttachmentLibrary\Support;

/**
 * Turns the flat list of directories on a disk into the nested, sorted nodes of the directory tree.
 *
 * Paths in a node are relative to the base path, because that is what the browser navigates with.
 *
 * @phpstan-type Node array{name: string, path: string, count: int, children: list<array<string, mixed>>}
 */
class DirectoryTree
{
    /**
     * @param  iterable<string>  $directories  Full paths, at any depth.
     * @param  array<string, int>  $counts  Number of files directly inside a directory, by full path.
     * @return list<Node>
     */
    public static function build(iterable $directories, ?string $basePath = null, array $counts = []): array
    {
        $basePath = Path::sanitize($basePath);
        $children = [];

        foreach ($directories as $directory) {
            $directory = Path::sanitize($directory);

            // The base path itself is the root of the tree, not a node in it.
            if (! Path::isBelow($directory, $basePath)) {
                continue;
            }

            // Register every ancestor too, so a listing that skips a level still yields a connected tree.
            $segments = explode('/', Path::relativeTo($directory, $basePath));

            foreach (array_keys($segments) as $index) {
                $path = implode('/', array_slice($segments, 0, $index + 1));
                $parent = implode('/', array_slice($segments, 0, $index));

                $children[$parent][$path] = $segments[$index];
            }
        }

        return self::nodes('', $children, $basePath, $counts);
    }

    /**
     * @param  array<string, array<string, string>>  $children  Directory names by path, by the path of their parent.
     * @param  array<string, int>  $counts
     * @return list<Node>
     */
    protected static function nodes(string $parent, array $children, ?string $basePath, array $counts): array
    {
        $names = $children[$parent] ?? [];

        // Always A to Z and never by case: "blog" belongs next to "Blog", not after "Zakelijk".
        uasort($names, fn (string $a, string $b) => strnatcasecmp($a, $b) ?: strcmp($a, $b));

        $nodes = [];

        foreach ($names as $path => $name) {
            $path = (string) $path;

            $nodes[] = [
                'name' => $name,
                'path' => $path,
                'count' => $counts[Path::join($basePath, $path)] ?? 0,
                'children' => self::nodes($path, $children, $basePath, $counts),
            ];
        }

        return $nodes;
    }
}
