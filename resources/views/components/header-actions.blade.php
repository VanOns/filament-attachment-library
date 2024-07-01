<div class="flex gap-4 justify-end mb-2 flex-wrap mt-2 md:mt-0 min-w-full md:min-w-[initial]"
     x-data="{showMimeOptions: false}"
     x-on:attachment-browser-loaded-js.window="showMimeOptions = $store.attachmentBrowser?.showMime()">

    {{-- Filtering & sorting  --}}

    {{-- Search --}}
    <x-filament::input.wrapper class="flex-1 min-w-full sm:min-w-[initial]">
        <x-filament::input type="text" wire:model.live="search"
                           placeholder="{{ __('filament-attachment-library::views.search') }}"/>
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

                        @foreach(\VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser::SORTABLE_FIELDS as $field)
                            <option value="{{$field}}">{{ __("filament-attachment-library::views.header-actions.sort.{$field}_ascending") }}</option>
                            <option value="!{{$field}}">{{ __("filament-attachment-library::views.header-actions.sort.{$field}_descending") }}</option>
                        @endforeach

                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-filament::dropdown.list.item>

            {{-- Page size --}}
            <x-filament::dropdown.list.item>
                <x-filament::input.wrapper class="flex-1 min-w-full md:min-w-[initial]">
                    <x-filament::input.select wire:model.live="pageSize">

                        @foreach(\VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser::PAGE_SIZES as $size)
                            <option value="{{$size}}">{{$size}}</option>
                        @endforeach

                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-filament::dropdown.list.item>

            {{-- MIME_TYPE --}}
            <x-filament::dropdown.list.item x-show="showMimeOptions">
                <x-filament::input.wrapper class="flex-1 min-w-full md:min-w-[initial]">
                    <x-filament::input.select wire:model.live="mime">

                        @foreach(\VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser::FILTERABLE_FILE_TYPES as $type => $mime)
                            <option value="{{$mime}}">{{__("filament-attachment-library::views.header-actions.filters.{$type}")}}</option>
                        @endforeach

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
