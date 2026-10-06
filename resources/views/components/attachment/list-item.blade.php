@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

@props(['attachment', 'selected' => false, 'selectableId' => null])

<x-filament-attachment-library::items.list-item
    :selected="$selected"
    :selectable-id="$selectableId"
    :title="$attachment->name"
    subtitle="{{ implode(' — ', array_filter([$attachment->extension, $attachment->duration, $attachment->size . ' MB'])) }}"
    {{ $attributes }}
>
    @isset($handle)
        <x-slot name="handle">
            {{ $handle }}
        </x-slot>
    @endisset
    @if($attachment->isImage())
        <img
            alt="{{ $attachment->alt }}"
            loading="lazy"
            src="{{ $attachment->thumbnailUrl() }}"
            class="object-cover size-full"
            draggable="false"
        >
    @endif

    @if($attachment->isVideo())
        @if($posterUrl = $attachment->posterUrl())
            <img
                alt=""
                loading="lazy"
                src="{{ $posterUrl }}"
                class="object-cover size-full"
                draggable="false"
            >
        @else
            <div class="relative size-full flex items-center justify-center">
                <x-filament::icon icon="heroicon-o-film" class="size-8" />
            </div>
        @endif
    @endif

    @if($attachment->isDocument())
        <x-filament::icon icon="heroicon-o-document-text" class="size-8" />
    @endif

    @isset($actions)
        <x-slot name="actions">
            {{ $actions }}
        </x-slot>
    @endisset
</x-filament-attachment-library::items.list-item>
