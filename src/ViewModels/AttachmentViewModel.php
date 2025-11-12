<?php

namespace VanOns\FilamentAttachmentLibrary\ViewModels;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Livewire\Wireable;
use VanOns\LaravelAttachmentLibrary\Enums\AttachmentType;
use VanOns\LaravelAttachmentLibrary\Facades\Glide;
use VanOns\LaravelAttachmentLibrary\Facades\Resizer;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class AttachmentViewModel implements Wireable
{
    public Attachment $attachment;

    public int $id;

    public string $name;

    public string $filename;

    public string $url;

    public ?string $path;

    public ?string $extension;

    public ?string $mimeType;

    public float $size;

    public string $createdBy;

    public ?Carbon $createdAt;

    public string $updatedBy;

    public ?Carbon $updatedAt;

    public ?string $title;

    public ?string $description;

    public ?string $alt;

    public ?string $caption;

    public ?int $bits = null;

    public ?int $channels = null;

    public ?string $dimensions = null;

    public function __construct(Attachment $attachment)
    {
        $userModel = Config::get('filament-attachment-library.user_model', User::class);
        $usernameProperty = Config::get('filament-attachment-library.username_property', 'name');

        $this->attachment = $attachment;

        $this->id = $attachment->id;
        $this->name = $attachment->name;
        $this->filename = $attachment->filename;
        $this->url = $attachment->url;
        $this->path = $attachment->path;
        $this->extension = Str::of($attachment->filename)->afterLast('.')->upper()->toString();
        $this->mimeType = $attachment->mime_type;
        $this->size = round($attachment->size / 1024 / 1024, 2);
        $this->createdBy = $userModel::find($attachment->created_by)->{$usernameProperty};
        $this->createdAt = $attachment->created_at; // @phpstan-ignore-line
        $this->updatedBy = $userModel::find($attachment->updated_by)->{$usernameProperty};
        $this->updatedAt = $attachment->updated_at; // @phpstan-ignore-line

        $this->title = $attachment->title;
        $this->description = $attachment->description;
        $this->alt = $attachment->alt;
        $this->caption = $attachment->caption;

        if ($metadata = $attachment->metadata) { // @phpstan-ignore-line
            $this->bits = $metadata->bits;
            $this->channels = $metadata->channels;
            $this->dimensions = "{$metadata->width}x{$metadata->height}";
        }
    }

    public function isAttachment(): bool
    {
        return true;
    }

    public function isDirectory(): bool
    {
        return false;
    }

    public function isImage(): bool
    {
        return $this->attachment->isType(AttachmentType::PREVIEWABLE_IMAGE);
    }

    public function isVideo(): bool
    {
        return $this->attachment->isType(AttachmentType::PREVIEWABLE_VIDEO);
    }

    public function isDocument(): bool
    {
        return !$this->isVideo() && !$this->isImage();
    }

    public function isSelected(array $selected): bool
    {
        return in_array($this->attachment->id, $selected);
    }

    public function thumbnailUrl(): ?string
    {
        return match(Glide::imageIsSupported($this->attachment->full_path)) {
            true => Resizer::src($this->attachment)->height(320)->resize()['url'] ?? null,
            default => $this->attachment->url,
        };
    }

    public function toLivewire()
    {
        return [ 'id' => $this->attachment->id ];
    }

    public static function fromLivewire($value): ?AttachmentViewModel
    {
        $attachment = Attachment::find($value['id']);

        if (!$attachment) {
            return null;
        }

        return new AttachmentViewModel($attachment);
    }
}
