@props([
    'withContextMenu' => true,
])

<div x-data="attachmentActions({ attachment })" {{ $attributes->only('class') }}>

    @if($withContextMenu)
        <x-filament::dropdown
            placement="bottom-end"
            x-ref="dropdown"
            x-show="showDropdown"
            x-on:click.stop=""
        >
            <x-slot name="trigger">
                <x-filament::icon
                    x-show="showIcon"
                    icon="heroicon-o-ellipsis-vertical"
                    class="w-8 h-8 m-0 toggle"
                />
            </x-slot>

            <x-filament::dropdown.list x-show="isAttachment">
                <x-filament::dropdown.list.item x-on:click="viewDetails()" class="flex md:hidden">
                    {{ __('filament-attachment-library::views.actions.attachment.view') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item x-on:click="openFile()">
                    {{ __('filament-attachment-library::views.actions.attachment.open') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item x-on:click="modifyFile()">
                    {{ __('filament-attachment-library::views.actions.attachment.edit') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item color="danger" x-on:click="removeFile()">
                    {{ __('filament-attachment-library::views.actions.attachment.delete') }}
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>

            <x-filament::dropdown.list x-show="isDirectory">
                <x-filament::dropdown.list.item x-on:click="viewDetails()" class="flex md:hidden">
                    {{ __('filament-attachment-library::views.actions.attachment.view') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item x-on:click="renameDirectory()">
                    {{ __('filament-attachment-library::views.actions.directory.rename') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item color="danger" x-on:click="removeDirectory()">
                    {{ __('filament-attachment-library::views.actions.directory.delete') }}
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    @endif

    <x-filament::icon
        icon="heroicon-o-check"
        class="w-8 h-8 m-0 block group-hover:hidden"
        x-show="showSelectIcons"
    />
    <x-filament::icon
        icon="heroicon-o-x-circle"
        class="w-8 h-8 m-0 hidden group-hover:block"
        x-show="showSelectIcons"
    />

</div>
