@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\DirectoryViewModel $directory
     */
@endphp

@props(['directory', 'triggerClass' => ''])

<div {{ $attributes->only('class') }}>
    <x-filament::dropdown>
        <x-slot name="trigger">
            <button type="button" class="{{ $triggerClass }}">
                <x-filament::icon icon="heroicon-o-ellipsis-vertical" class="w-8 h-8 stroke-white"/>
            </button>
        </x-slot>
        <x-filament::dropdown.list x-on:mouseleave="$refs.panel.close()">
            <x-filament::dropdown.list.item
                    wire:click="mountAction('renameDirectory', { name: '{{ $directory->name }}', full_path: '{{ $directory->fullPath }}' })"
            >
                {{ __('filament-attachment-library::views.actions.directory.rename') }}
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item
                    color="danger"
                    wire:click="mountAction('deleteDirectory', { full_path: '{{ $directory->fullPath }}' })"
            >
                {{ __('filament-attachment-library::views.actions.directory.delete') }}
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
