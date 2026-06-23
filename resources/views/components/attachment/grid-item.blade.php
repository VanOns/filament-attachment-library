@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

@props(['attachment', 'selected' => false, 'selectableId' => null])

<x-filament-attachment-library::items.grid-item
        :selected="$selected"
        :selectable-id="$selectableId"
        :title="$attachment->name"
        subtitle="{{$attachment->extension}} — {{ $attachment->size }} MB"
        {{ $attributes }}
>
    @isset($actions)
        <x-slot name="actions">
            {{ $actions }}
        </x-slot>
    @endisset

    @if($attachment->isImage())
        <img
            alt="{{ $attachment->alt }}"
            loading="lazy"
            src="{{ $attachment->thumbnailUrl() }}"
            class="object-contain size-full"
            draggable="false"
        >
    @endif

    @if($attachment->isVideo())
        {{-- The icon sits behind the video: when the browser cannot decode the format,
             the video element stays transparent and the icon shows through. --}}
        <div class="relative size-full flex items-center justify-center">
            <x-filament::icon icon="heroicon-o-film" class="size-20" />
        </div>
    @endif

    @if($attachment->isDocument())
        <x-filament::icon icon="heroicon-o-document-text" class="size-20" />
    @endif
</x-filament-attachment-library::items.grid-item>
