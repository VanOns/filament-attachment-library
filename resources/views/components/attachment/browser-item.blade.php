@php
    use VanOns\FilamentAttachmentLibrary\Enums\Layout;
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

@props(['attachment', 'selected' => false, 'layout' => Layout::GRID])

@if($layout === Layout::GRID)
    <x-filament-attachment-library::attachment.grid-item
            :attachment="$attachment"
            :selected="$selected"
            wire:click="selectAttachment({{ json_encode($attachment->id) }})"
    >
        <x-slot name="actions">
            <x-filament-attachment-library::attachment.browser-actions
                    :attachment="$attachment"
                    trigger-class="p-1 bg-white dark:bg-black shadow-xs rounded-md border border-black/10 dark:border-white/10 opacity-0 group-hover:opacity-100 transition"
            />
        </x-slot>
    </x-filament-attachment-library::attachment.grid-item>
@endif

@if($layout === Layout::LIST)
    <x-filament-attachment-library::attachment.list-item
            :attachment="$attachment"
            :selected="$selected"
            wire:click="selectAttachment({{ json_encode($attachment->id) }})"
    >
        <x-slot name="actions">
            <x-filament-attachment-library::attachment.browser-actions :attachment="$attachment" />
        </x-slot>
    </x-filament-attachment-library::attachment.list-item>
@endif
