@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\DirectoryViewModel $directory
     */
@endphp

@props(['directory'])

<x-filament-attachment-library::items.grid-item
    :title="$directory->name"
    :subtitle="trans_choice('filament-attachment-library::views.browser.file_count', $directory->itemCount(), ['count' => $directory->itemCount()])"
    {{ $attributes }}
>
    @isset($actions)
        <x-slot name="actions">
            {{ $actions }}
        </x-slot>
    @endisset

    <x-filament::icon icon="heroicon-o-folder" class="size-20" />
</x-filament-attachment-library::items.grid-item>
