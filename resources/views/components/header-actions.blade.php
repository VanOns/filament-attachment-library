<div class="flex gap-4 justify-end mb-2 flex-wrap mt-2 md:mt-0 min-w-full md:min-w-[initial]" x-data="{showMimeOption: false}" x-on:attachment-browser-loaded-js.window="showMimeOption = $store.attachmentBrowser?.showMime()">

    {{-- Filtering & sorting  --}}

    {{-- Search --}}
    <x-filament::input.wrapper class="flex-1 min-w-full sm:min-w-[initial]">
        <x-filament::input type="text" wire:model.live="search" placeholder="{{ __('filament-attachment-library::views.search') }}" />
    </x-filament::input.wrapper>

    <x-filament::dropdown placement="bottom-start">

        <x-slot name="trigger">
            <x-filament::button color="gray">
                <h1>{{ __('filament-attachment-library::views.header-actions.options') }}</h1>
            </x-filament::button>
        </x-slot>

        <x-filament::dropdown.list>

            {{-- Sort by --}}
            <x-filament::dropdown.list.item>
                <x-filament::input.wrapper class="flex-1 min-w-full md:min-w-[initial]">
                    <x-filament::input.select wire:model.live="sortBy">
                        <option value="created_at">{{ __('filament-attachment-library::views.header-actions.created_at_ascending') }}</option>
                        <option value="!created_at">{{ __('filament-attachment-library::views.header-actions.created_at_descending') }}</option>
                        <option value="name">{{ __('filament-attachment-library::views.header-actions.name_ascending') }}</option>
                        <option value="!name">{{ __('filament-attachment-library::views.header-actions.name_descending') }}</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-filament::dropdown.list.item>

            {{-- Page size --}}
            <x-filament::dropdown.list.item>
                <x-filament::input.wrapper class="flex-1 min-w-full md:min-w-[initial]">
                    <x-filament::input.select wire:model.live="pageSize">
                        <option value="5">5</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-filament::dropdown.list.item>

            {{-- MIME_TYPE --}}
            <x-filament::dropdown.list.item x-show="showMimeOption">
                <x-filament::input.wrapper class="flex-1 min-w-full md:min-w-[initial]">
                    <x-filament::input.select wire:model.live="mime">
                        <option value="">Alle</option>
                        <option value="image/*">Afbeeldingen</option>
                        <option value="audio/*">Audio</option>
                        <option value="application/pdf">PDF</option>
                        <option value="video/*">Video</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>

    </x-filament::dropdown>

    <x-filament::button color="gray" x-on:click="$dispatch('show-form', {form: 'createDirectory'})" icon="heroicon-o-folder-plus">
        <h1>{{ __('filament-attachment-library::views.actions.directory.create') }}</h1>
    </x-filament::button>

    <x-filament::button color="primary" x-on:click="$dispatch('show-form', {form: 'uploadAttachment'})" icon="heroicon-o-arrow-up-tray">
        <h1>{{ __('filament-attachment-library::views.actions.attachment.upload') }}</h1>
    </x-filament::button>
</div>
