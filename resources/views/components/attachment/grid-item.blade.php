@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

@props(['attachment', 'selected' => false])

<x-filament-attachment-library::items.grid-item
        :selected="$selected"
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
            <video
                src="{{ $attachment->url }}#t=0.1"
                preload="metadata"
                muted
                playsinline
                tabindex="-1"
                class="absolute inset-0 object-cover size-full pointer-events-none"
            ></video>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="rounded-full bg-black/50 p-2">
                    {{-- Nudged right: a play triangle's visual mass sits left of center. --}}
                    <x-filament::icon icon="heroicon-s-play" class="size-8 translate-x-0.5 text-white" />
                </div>
            </div>
        </div>
    @endif

    @if($attachment->isDocument())
        <x-filament::icon icon="heroicon-o-document-text" class="size-20" />
    @endif
</x-filament-attachment-library::items.grid-item>
