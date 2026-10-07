<?php

namespace VanOns\FilamentAttachmentLibrary\ViewModels;

use Carbon\CarbonInterface;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Wireable;
use VanOns\LaravelAttachmentLibrary\Enums\AttachmentType;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
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

    public ?string $createdBy;

    public ?CarbonInterface $createdAt;

    public ?string $updatedBy;

    public ?CarbonInterface $updatedAt;

    public ?string $title;

    public ?string $description;

    public ?string $alt;

    public ?string $caption;

    public ?int $bits = null;

    public ?int $channels = null;

    public ?int $width = null;

    public ?int $height = null;

    public ?string $dimensions = null;

    public ?string $duration = null;

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
        $this->createdBy = $userModel::find($attachment->created_by)?->{$usernameProperty};
        $this->createdAt = $attachment->created_at; // @phpstan-ignore-line
        $this->updatedBy = $userModel::find($attachment->updated_by)?->{$usernameProperty};
        $this->updatedAt = $attachment->updated_at; // @phpstan-ignore-line

        $this->title = $attachment->title;
        $this->description = $attachment->description;
        $this->alt = $attachment->alt;
        $this->caption = $attachment->caption;

        // Videos are probed on upload; reading the metadata would run ffprobe for every listed video.
        if ($this->isVideo()) {
            $this->width = $attachment->width;
            $this->height = $attachment->height;
            $this->duration = $attachment->duration !== null ? $this->formatDuration($attachment->duration) : null;
        } elseif ($metadata = AttachmentManager::getMetadata($attachment)) {
            $this->bits = $metadata->bits;
            $this->channels = $metadata->channels;
            $this->width = $metadata->width;
            $this->height = $metadata->height;
        }

        $this->dimensions = $this->width ? "{$this->width}x{$this->height}" : null;
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

    /**
     * Plain-text files, including subtitles, which are stored with varying mime types.
     */
    public function isText(): bool
    {
        return Str::startsWith((string) $this->mimeType, 'text/') || $this->isSubtitle();
    }

    /**
     * Subtitle files are recognised by extension, since their mime type varies.
     */
    public function isSubtitle(): bool
    {
        return in_array(strtolower($this->attachment->extension), ['srt', 'vtt']);
    }

    /**
     * Return the icon that represents the attachment's type.
     */
    public function icon(): string
    {
        return match (true) {
            $this->isImage() => 'heroicon-o-photo',
            $this->isVideo() => 'heroicon-o-video-camera',
            $this->isSubtitle() => 'heroicon-o-chat-bubble-bottom-center-text',
            default => 'heroicon-o-document-text',
        };
    }

    /**
     * Return the first lines of a text file, or null for other files.
     */
    public function textPreview(int $lines = 10): ?string
    {
        if (!$this->isText()) {
            return null;
        }

        $stream = Storage::disk($this->attachment->disk)->readStream($this->attachment->full_path);

        if (!$stream) {
            return null;
        }

        $preview = [];

        while (count($preview) < $lines && ($line = fgets($stream, 1024)) !== false) {
            $preview[] = rtrim($line, "\r\n");
        }

        fclose($stream);

        return implode("\n", $preview);
    }

    /**
     * Return the closest common aspect ratio (e.g. 16:9), or null when none is close.
     */
    public function aspectRatioLabel(): ?string
    {
        if (!$this->width || !$this->height) {
            return null;
        }

        return collect(['1:1', '5:4', '4:3', '3:2', '16:10', '16:9', '21:9', '4:5', '3:4', '2:3', '9:16'])
            ->first(function (string $label) {
                [$x, $y] = array_map('intval', explode(':', $label));

                return abs(($this->width / $this->height) / ($x / $y) - 1) < 0.01;
            });
    }

    /**
     * @return Collection<int, string>
     */
    public function captionLabels(): Collection
    {
        return $this->attachment->captions->map(fn (Attachment $caption) => static::captionLabel($caption));
    }

    public static function captionLabel(Attachment $caption): string
    {
        return $caption->pivot->label ?: $caption->pivot->language;
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

    public function posterUrl(): ?string
    {
        $poster = $this->attachment->poster;

        if (!$poster) {
            return null;
        }

        return Resizer::src($poster)->height(320)->resize()['url'] ?? $poster->url;
    }

    protected function formatDuration(float $duration): string
    {
        $seconds = (int) round($duration);

        return $seconds >= 3600
            ? sprintf('%d:%02d:%02d', intdiv($seconds, 3600), intdiv($seconds % 3600, 60), $seconds % 60)
            : sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60);
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
