@if($renderAttachmentBrowserModal)

    <x-filament::modal width="7xl" id="attachment-modal" sticky-footer>

        <x-slot name="heading">
            {{ __('filament-attachment-library::views.title') }}
        </x-slot>

        <livewire:attachment-browser :inModal="true" />

        <x-slot name="footer">

            <div class="flex gap-4">
                <x-filament::button color="primary" x-on:click="$dispatch('close-modal', {id: 'attachment-modal', save: true})">
                    {{ __('filament-attachment-library::views.submit') }}
                </x-filament::button>

                <x-filament::button color="gray" x-on:click="$dispatch('close-modal', {id: 'attachment-modal'})">
                    {{ __('filament-attachment-library::views.close') }}
                </x-filament::button>
            </div>

        </x-slot>

    </x-filament::modal>

@endif
