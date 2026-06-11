@props(['statePath', 'multiple', 'mime', 'disableMimeFilter'])

<x-filament::modal width="7xl" id="attachment-modal" sticky-footer>
    <x-slot name="heading">
        {{ __('filament-attachment-library::views.title') }}
    </x-slot>

    <div
        {{-- The browser is lazy-loaded, so it misses events dispatched before its first load (e.g. the
             open-attachment-modal payload carrying the statePath when the modal is first opened).
             Buffer the latest payload and replay it once the component announces itself. --}}
        x-data="{ pendingOpen: null }"
        x-on:open-attachment-modal.window="pendingOpen = $event.detail"
        x-on:attachment-browser-loaded.window="if (pendingOpen) { $dispatch('open-attachment-modal', pendingOpen); pendingOpen = null }"
    >
        <livewire:attachment-browser :basePath="$basePath" lazy />
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
