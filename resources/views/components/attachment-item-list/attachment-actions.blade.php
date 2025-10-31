@props(['viewModel'])

<div {{ $attributes->only('class') }}>
    <x-filament::dropdown>
        <x-slot name="trigger">
            <x-filament::icon
                icon="heroicon-o-ellipsis-vertical"
                class="w-8 h-8"
            />
        </x-slot>

        <x-filament::dropdown.list>
            @if($viewModel->isAttachment())
                <x-filament::dropdown.list.item
                    class="flex md:hidden"
                    x-on:click="
                        $dispatch('highlight-attachment', { id: {{ json_encode($viewModel->attachment->id) }} });
                        $dispatch('open-modal', { id: 'attachment-info-modal' });
                    "
                >
                    {{ __('filament-attachment-library::views.actions.attachment.view') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item tag="a" :href="$viewModel->attachment->url" target="_blank">
                    {{ __('filament-attachment-library::views.actions.attachment.open') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item
                    wire:click="mountAction('editAttachmentAction', { attachment_id: {{ json_encode($viewModel->attachment->id) }}})"
                >
                    {{ __('filament-attachment-library::views.actions.attachment.edit') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item
                    color="danger"
                    wire:click="mountAction('deleteAttachment', { attachment_id: {{ json_encode($viewModel->attachment->id) }}})"
                >
                    {{ __('filament-attachment-library::views.actions.attachment.delete') }}
                </x-filament::dropdown.list.item>
            @endif

            @if($viewModel->isDirectory())
                <x-filament::dropdown.list.item
                    wire:click="mountAction('renameDirectory', { name: '{{ $viewModel->directory->name }}', full_path: '{{ $viewModel->directory->fullPath }}' })"
                >
                    {{ __('filament-attachment-library::views.actions.directory.rename') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item
                    color="danger"
                    wire:click="mountAction('deleteDirectory', { full_path: '{{ $viewModel->directory->fullPath }}' })"
                >
                    {{ __('filament-attachment-library::views.actions.directory.delete') }}
                </x-filament::dropdown.list.item>
            @endif
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
