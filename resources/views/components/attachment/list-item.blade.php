@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

@props(['attachment', 'selected'])

<x-filament-attachment-library::items.list-item
    :selected="$selected"
    :title="$attachment->name"
    subtitle="{{ $attachment->size }} MB"
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

{{--    @if($attachment->isImage())--}}
{{--        <img--}}
{{--            alt="{{ $attachment->attachment->alt }}"--}}
{{--            loading="lazy"--}}
{{--            src="{{ $attachment->thumbnailUrl() }}"--}}
{{--            class="relative rounded-lg overflow-hidden h-12 w-12 object-center object-cover ring-1 ring-gray-950/10 dark:ring-white/10"--}}
{{--        >--}}
{{--    @else--}}
{{--        <div class="w-12 flex justify-center items-center">--}}
{{--            @if($attachment->isVideo())--}}
{{--                <x-filament::icon icon="heroicon-o-film" class="size-8"/>--}}
{{--            @endif--}}

{{--            @if($attachment->isDocument())--}}
{{--                <x-filament::icon icon="heroicon-o-document" class="size-8"/>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    @endif--}}

    @isset($actions)
        <x-slot name="actions">
            {{ $actions }}
        </x-slot>
    @endisset
</x-filament-attachment-library::items.list-item>
