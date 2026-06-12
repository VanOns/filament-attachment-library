@props([
    'class' => '',
    'currentPath' => null,
    'selected' => []
])

<div @class([ 'flex-1 max-w-md', $class ])>

    {{-- Upload & create directory (mobile has its own buttons in the header) --}}
    <div class="hidden md:flex flex-col gap-2 mb-4">
        <x-filament::button icon="heroicon-o-arrow-up-tray" class="w-full" x-on:click="openFileDialog()">
            {{ __('filament-attachment-library::views.actions.attachment.upload') }}
        </x-filament::button>

        <x-filament::button color="gray" icon="heroicon-o-folder-plus" class="w-full" wire:click="mountAction('createDirectory')">
            {{ __('filament-attachment-library::views.actions.directory.create') }}
        </x-filament::button>
    </div>

    @if(count($selected) > 1)
        <x-filament::section class="mb-4">
            <p>{{ __('filament-attachment-library::views.sidebar.files_selected', ['count' => count($selected)]) }}</p>
        </x-filament::section>
    @endif

    {{-- Attachment info section --}}
    {{-- Sticky offset is a variable: the default clears the page topbar, the modal wrapper overrides it --}}
    <livewire:attachment-info
        :$selected
        :$currentPath
        class="hidden md:block md:sticky md:top-[var(--fal-info-top,6rem)]"
    />
    <x-filament-attachment-library::attachment-info-modal :$selected :$currentPath/>
</div>
