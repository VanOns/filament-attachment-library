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
        $thumbnailUrl = $target->isType(AttachmentType::PREVIEWABLE_IMAGE)
            ? glide_url($target->full_path)->preset('small')->url()
            : null;

        return [[
            'id' => $target->id,
            'path' => $target->path,
            'name' => $target->name,
            'thumb_url' => $thumbnailUrl,
            'url' => $target->url,
            'created_at' => $target->created_at->translatedFormat('d F Y'),
            'mime_type' => $target->mime_type,
            'alt' => $target->alt,
            'title' => $target->title,
            'description' => $target->description,
            'caption' => $target->caption,
            'class' => 'attachment',
            'is_image' => $target->isType(AttachmentType::PREVIEWABLE_IMAGE),
            'is_video' => $target->isType(AttachmentType::PREVIEWABLE_VIDEO),
            'is_audio' => $target->isType(AttachmentType::PREVIEWABLE_AUDIO),
            'is_renderable' => AttachmentType::isRenderable($target->type),
            'size' => round(($target->size / 1024 / 1024), 2),
        ], []];
    }

    public function hydrate($value)
    {
        return Attachment::find($value['id']);
    }
}
