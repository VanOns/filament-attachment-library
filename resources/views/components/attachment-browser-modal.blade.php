@props(['statePath', 'multiple', 'mime', 'disableMimeFilter'])

<x-filament::modal width="7xl" id="attachment-modal-{{$statePath}}" sticky-footer>
    <x-slot name="heading">
        {{ __('filament-attachment-library::views.title') }}
    </x-slot>

    <livewire:attachment-browser
        :wire:key="$statePath"
        :multiple="$multiple"
        :statePath="$statePath"
        :mime="$mime"
        :disableMimeFilter="$disableMimeFilter"
    />

    <x-slot name="footer">
        <div class="flex gap-4">
            <x-filament::button color="primary" x-on:click="$dispatch('close-modal', {id: 'attachment-modal-{{$statePath}}', save: true, statePath: '{{$statePath}}'})"
            >
                {{ __('filament-attachment-library::views.submit') }}
            </x-filament::button>

            <x-filament::button color="gray" x-on:click="$dispatch('close-modal', {id: 'attachment-modal-{{$statePath}}'})">
                {{ __('filament-attachment-library::views.close') }}
            </x-filament::button>
        </div>
    </x-slot>
</x-filament::modal>
