@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $directory
     */
@endphp

@props(['directory'])

<x-filament-attachment-library::items.list-item
    :title="$directory->name"
    :subtitle="trans_choice('filament-attachment-library::views.browser.file_count',  $directory->itemCount(), ['count' => $directory->itemCount()])"
    {{ $attributes }}
>
    <x-filament::icon icon="heroicon-o-folder" class="size-8"/>

    @isset($actions)
        <x-slot name="actions">
            {{ $actions }}
        </x-slot>
    @endisset
</x-filament-attachment-library::items.list-item>
