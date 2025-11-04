@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $directory
     */
@endphp

@props(['directory'])

<x-filament-attachment-library::items.list-item
    :title="$directory->name"
    subtitle="13 FILES"
    {{ $attributes }}
>
    <x-filament::icon icon="heroicon-o-folder" class="size-8"/>

    @isset($actions)
        <x-slot name="actions">
            {{ $actions }}
        </x-slot>
    @endisset
</x-filament-attachment-library::items.list-item>
