<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire\Synthesizers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Foundation\Auth\User;
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
            'created_at' => $target->created_at->translatedFormat('d F Y'),
            'created_by' => User::find($target->created_by)?->name,
            'updated_at' => $target->updated_at->translatedFormat('d F Y'),
            'updated_by' => User::find($target->updated_by)?->name,
            'mime_type' => $target->mime_type,
            'bits' => $target->bits,
            'channels' => $target->channels,
            'dimensions' => $target->dimensions,
            'alt' => $target->alt,
            'title' => $target->title,
            'description' => $target->description,
            'caption' => $target->caption,
            'class' => 'attachment',
            'is_image' => $target->isType(AttachmentType::PREVIEWABLE_IMAGE),
            'is_video' => $target->isType(AttachmentType::PREVIEWABLE_VIDEO),
            'size' => round(($target->size / 1024 / 1024), 2),
        ], []];
    }

    public function hydrate($value)
    {
        return Attachment::find($value['id']);
    }
}
