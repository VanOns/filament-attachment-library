<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire\Synthesizers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use VanOns\LaravelAttachmentLibrary\Enums\AttachmentType;
use VanOns\LaravelAttachmentLibrary\Facades\Glide;
use VanOns\LaravelAttachmentLibrary\Facades\Resizer;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class AttachmentSynth extends Synth
{
    public static $key = 'attachment';

    public static function match($target): bool
    {
        return $target instanceof Attachment;
    }

    public function dehydrate($target): array
    {
        $userModel = Config::get('filament-attachment-library.user_model', User::class);
        $usernameProperty = Config::get('filament-attachment-library.username_property', 'name');

        $fields = [
            'id' => $target->id,
            'path' => $target->path,
            'name' => $target->name,
            'url' => $target->url,
            'created_at' => $target->created_at->translatedFormat('d F Y'),
            'created_by' => $userModel::find($target->created_by)?->{$usernameProperty},
            'updated_at' => $target->updated_at->translatedFormat('d F Y'),
            'updated_by' => $userModel::find($target->updated_by)?->{$usernameProperty},
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

        if ($target->isImage()) {
            $fields['thumbnail_url'] = match(Glide::imageIsSupported($target->full_path)) {
                true => Resizer::src($target)->height(200)->resize()['url'] ?? null,
                default => $target->url,
            };

            if ($metadata = $target->metadata) {
                $fields['bits'] = $metadata->bits;
                $fields['channels'] = $metadata->channels;
                $fields['dimensions'] = "{$metadata->width}x{$metadata->height}";
            }
        }

        return [$fields, []];
    }

    public function hydrate($value): ?Attachment
    {
        return Attachment::find($value['id']);
    }
}
