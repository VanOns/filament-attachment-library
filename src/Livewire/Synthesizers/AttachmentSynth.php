<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire\Synthesizers;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use VanOns\LaravelAttachmentLibrary\Enums\AttachmentType;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class AttachmentSynth extends Synth
{
    public static $key = 'attachment';

    public static function match($target)
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
            'created_at' => $target->created_at,
            'mime_type' => $target->mime_type,
            'type' => 'attachment',
            'previewable' => $target->isType(AttachmentType::PREVIEWABLE),
            'size' => round(($target->size / 1024 / 1024), 2),
        ], []];
    }

    public function hydrate($value)
    {
        return Attachment::find($value['id']);
    }
}
