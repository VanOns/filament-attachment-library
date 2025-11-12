@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

@props(['attachment', 'selected'])

<x-filament-attachment-library::items.list-item
    :selected="$selected"
    :title="$attachment->name"
    subtitle="{{$attachment->extension}} — {{ $attachment->size }} MB"
    {{ $attributes }}
>
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
        <x-filament::icon icon="heroicon-o-film" class="size-8" />
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
