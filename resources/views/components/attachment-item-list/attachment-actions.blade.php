@props([
    'withContextMenu' => true,
])

<div {{ $attributes->only('class') }}>
    @if($withContextMenu)
        <x-filament::dropdown
            placement="bottom-end"
            x-show="$store.attachmentBrowser?.showActions(statePath)"
            x-on:click.stop=""
        >
            <x-slot name="trigger">
                <x-filament::icon
                    x-show="
                        (attachment.class === 'attachment' && !$store.attachmentBrowser?.isSelected(attachment.id, statePath))
                        || (attachment.class === 'directory')
                    "
                    icon="heroicon-o-ellipsis-vertical"
                    class="w-8 h-8 m-0 toggle"
                />
            </x-slot>

            <x-filament::dropdown.list x-show="attachment.class === 'attachment'">
                <x-filament::dropdown.list.item x-on:click="window.open(attachment.url)">
                    {{ __('filament-attachment-library::views.actions.attachment.open') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item x-on:click="$dispatch('mount-action', {name: 'editAttachmentAction', arguments: {'attachment_id': attachment.id}})">
                    {{ __('filament-attachment-library::views.actions.attachment.edit') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item color="danger" x-on:click="$dispatch('mount-action', {name: 'deleteAttachment', arguments: {'attachment_id': attachment.id}})">
                    {{ __('filament-attachment-library::views.actions.attachment.delete') }}
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>

            <x-filament::dropdown.list x-show="attachment.class === 'directory'">
                <x-filament::dropdown.list.item x-on:click="$dispatch('mount-action', {name: 'renameDirectory', arguments: {'directory': attachment}})">
                    {{ __('filament-attachment-library::views.actions.directory.rename') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item color="danger" x-on:click="$dispatch('mount-action', {name: 'deleteDirectory', arguments: {'directory': attachment}})">
                    {{ __('filament-attachment-library::views.actions.directory.delete') }}
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    @endif

    <x-filament::icon
        icon="heroicon-o-check"
        class="w-8 h-8 m-0 block group-hover:hidden"
        x-show="attachment.class === 'attachment' && $store.attachmentBrowser?.isSelected(attachment.id, statePath)"
    />
    <x-filament::icon
        icon="heroicon-o-x-circle"
        class="w-8 h-8 m-0 hidden group-hover:block"
        x-show="attachment.class === 'attachment' && $store.attachmentBrowser?.isSelected(attachment.id, statePath)"
    />
</div>
