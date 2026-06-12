@props(['statePath', 'multiple', 'mime', 'disableMimeFilter'])

<x-filament::modal width="7xl" id="attachment-modal" sticky-footer>
    <x-slot name="heading">
        {{ __('filament-attachment-library::views.title') }}
    </x-slot>

    <div
        {{-- No topbar to clear inside the modal: shrink the info panel's sticky offset --}}
        class="[--fal-info-top:1rem]"
        x-data="attachmentModalBuffer"
    >
        {{-- trackUrl off: the page browser owns the query string; a second tracking
             instance pollutes the URL and hydrates itself on every history pop --}}
        <livewire:attachment-browser :basePath="$basePath" :trackUrl="false" lazy />
    </div>

    <x-slot name="footer">
        <div class="flex gap-4">
            <x-filament::button color="primary" x-on:click="$dispatch('close-modal', {id: 'attachment-modal', save: true})"
            >
                {{ __('filament-attachment-library::views.submit') }}
            </x-filament::button>

            <x-filament::button color="gray" x-on:click="$dispatch('close-modal', {id: 'attachment-modal'})">
                {{ __('filament-attachment-library::views.close') }}
            </x-filament::button>
        </div>
    </x-slot>
</x-filament::modal>
