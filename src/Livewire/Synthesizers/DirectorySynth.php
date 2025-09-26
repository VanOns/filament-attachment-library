<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire\Synthesizers;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use VanOns\LaravelAttachmentLibrary\DataTransferObjects\Directory;

class DirectorySynth extends Synth
{
    public static $key = 'directory';

    public static function match($target): bool
    {
        return $target instanceof Directory;
    }

    public function dehydrate(Directory $target): array
    {
        return [[
            'path' => $target->path,
            'name' => $target->name,
            'fullPath' => $target->fullPath,
            'class' => 'directory',
        ], []];
    }

    public function hydrate($value): Directory
    {
        return new Directory($value['fullPath']);
    }
}
