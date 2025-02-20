<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire\Synthesizers;

use Illuminate\Foundation\Auth\User;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use VanOns\LaravelAttachmentLibrary\Enums\AttachmentType;
use VanOns\LaravelAttachmentLibrary\Facades\Resizer;
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
        /* @var ?User $userCreated */
        $userCreated = User::find($target->created_by);

        /* @var ?User $userUpdated */
        $userUpdated = User::find($target->created_by);

        $fields = [
            'id' => $target->id,
            'path' => $target->path,
            'name' => $target->name,
            'url' => $target->url,
            'created_at' => $target->created_at->translatedFormat('d F Y'),
            'created_by' => $userCreated?->name,
            'updated_at' => $target->updated_at->translatedFormat('d F Y'),
            'updated_by' => $userUpdated?->name,
            'mime_type' => $target->mime_type,
            'alt' => $target->alt,
            'title' => $target->title,
            'description' => $target->description,
            'caption' => $target->caption,
            'class' => 'attachment',
            'is_image' => $target->isType(AttachmentType::PREVIEWABLE_IMAGE),
            'is_video' => $target->isType(AttachmentType::PREVIEWABLE_VIDEO),
            'size' => round(($target->size / 1024 / 1024), 2),
        ];

        $metadata = $target->metadata;

        if ($target->isType(AttachmentType::PREVIEWABLE_IMAGE)) {
            $fields['thumbnail_url'] = Resizer::src($target)->height(200)->resize()['url'];
        }

        if ($metadata && $target->isType(AttachmentType::PREVIEWABLE_IMAGE)) {
            $fields['bits'] = $metadata->bits;
            $fields['channels'] = $metadata->channels;
            $fields['dimensions'] = "{$metadata->width}x{$metadata->height}";
        }

        return [$fields, []];
    }

    public function hydrate($value)
    {
        return Attachment::find($value['id']);
    }
}
