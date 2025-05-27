<x-filament::modal width="7xl" id="attachment-info-modal" sticky-footer>

    <x-slot name="heading">
        {{ __('filament-attachment-library::views.info.details.modal_title') }}
    </x-slot>

    <livewire:attachment-info />

    <x-slot name="footer">

        <div class="flex gap-4">
            <x-filament::button color="gray" x-on:click="$dispatch('close-modal', {id: 'attachment-info-modal'})">
                {{ __('filament-attachment-library::views.close') }}
            </x-filament::button>
        </div>

    </x-slot>

</x-filament::modal>
