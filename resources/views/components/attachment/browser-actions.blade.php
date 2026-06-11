@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

@props(['attachment', 'triggerClass' => null])
<x-filament::dropdown>
    <x-slot name="trigger">
        <button type="button" @class($triggerClass)>
            <x-filament::icon icon="heroicon-o-ellipsis-vertical" class="size-6"/>
        </button>
    </x-slot>

    <x-filament::dropdown.list x-on:mouseleave="$refs.panel.close()">
        <x-filament::dropdown.list.item
            icon="heroicon-o-eye"
            class="flex md:hidden"
            x-on:click="
                $dispatch('highlight-attachment', { id: {{ json_encode($attachment->attachment->id) }} });
                $dispatch('open-modal', { id: 'attachment-info-modal' });
            "
        >
            {{ __('filament-attachment-library::views.actions.attachment.view') }}
        </x-filament::dropdown.list.item>

        <x-filament::dropdown.list.item icon="heroicon-o-arrow-top-right-on-square" tag="a" :href="$attachment->attachment->url" target="_blank">
            {{ __('filament-attachment-library::views.actions.attachment.open') }}
        </x-filament::dropdown.list.item>

        <x-filament::dropdown.list.item
            icon="heroicon-o-pencil-square"
            wire:click="mountAction('editAttachmentAction', { attachment_id: {{ json_encode($attachment->attachment->id) }}})"
        >
            {{ __('filament-attachment-library::views.actions.attachment.edit') }}
        </x-filament::dropdown.list.item>

        <x-filament::dropdown.list.item
            icon="heroicon-o-arrow-right-circle"
            wire:click="mountAction('moveAttachmentAction', { attachment_id: {{ json_encode($attachment->attachment->id) }}})"
        >
            {{ __('filament-attachment-library::views.actions.attachment.move') }}
        </x-filament::dropdown.list.item>

        <x-filament::dropdown.list.item
            icon="heroicon-o-arrow-path"
            wire:click="mountAction('replaceAttachmentAction', { attachment_id: {{ json_encode($attachment->attachment->id) }}})"
        >
            {{ __('filament-attachment-library::views.actions.attachment.replace') }}
        </x-filament::dropdown.list.item>

        <x-filament::dropdown.list.item
            icon="heroicon-o-trash"
            color="danger"
            wire:click="mountAction('deleteAttachment', { attachment_id: {{ json_encode($attachment->attachment->id) }}})"
        >
            {{ __('filament-attachment-library::views.actions.attachment.delete') }}
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>
