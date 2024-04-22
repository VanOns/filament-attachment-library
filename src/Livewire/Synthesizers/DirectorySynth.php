<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire\Synthesizers;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use VanOns\LaravelAttachmentLibrary\Directory;

class DirectorySynth extends Synth
{
    public static $key = 'directory';

    static function match($target)
    {
        return $target instanceof Directory;
    }

    public function dehydrate($target)
    {
        return [[
            'path' => $target->path,
            'name' => $target->name,
            'fullPath' => $target->fullPath,
            'type' => 'directory',
        ], []];
    }

    public function hydrate($value)
    {
        // ...
    }
}