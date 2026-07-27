@php
    use VanOns\FilamentAttachmentLibrary\Enums\Layout;
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $directory
     */
@endphp

@props(['directory', 'layout' => Layout::GRID])

<x-filament-attachment-library::items.list-item
        :title="$directory->name"
        :subtitle="trans_choice('filament-attachment-library::views.browser.file_count',  $directory->itemCount(), ['count' => $directory->itemCount()])"
        wire:click="openPath({{ Illuminate\Support\Js::from($directory->fullPath) }})"
        wire:loading.attr="disabled"
        wire:target="openPath"
>
    <x-filament::icon
        icon="heroicon-o-folder"
        class="size-8"
        wire:loading.remove
        wire:target="openPath({{ Illuminate\Support\Js::from($directory->fullPath) }})"
    />

    <x-filament::loading-indicator
        class="size-8"
        wire:loading
        wire:target="openPath({{ Illuminate\Support\Js::from($directory->fullPath) }})"
    />

    <x-slot name="actions">
        <x-filament-attachment-library::directory.browser-actions
            :directory="$directory"
            wire:loading.remove
            wire:target="openPath"
            :trigger-class="$layout === Layout::GRID
                ? 'absolute top-2 right-2 p-1 bg-white dark:bg-black shadow-xs rounded-md border border-black/10 dark:border-white/10 opacity-0 group-hover:opacity-100 transition'
                : null"
        />
    </x-slot>
</x-filament-attachment-library::items.list-item>
