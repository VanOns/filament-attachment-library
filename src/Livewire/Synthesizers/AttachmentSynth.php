<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire\Synthesizers;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class AttachmentSynth extends Synth
{
    public static $key = 'attachment';

    static function match($target)
    {
        return $target instanceof Attachment;
    }

    public function dehydrate($target)
    {
        return [[
            'id' => $target->id,
            'path' => $target->path,
            'name' => $target->name,
            'url' => $target->url,
            'type' => 'attachment',
            'hasThumbnail' => in_array($target->mime_type, ['image/jpeg', 'image/png']),
            'size' => round(($target->size / 1024 / 1024), 2),
        ], []];
    }

    public function hydrate($value)
    {
        // ...
    }
}