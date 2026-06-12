@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\Enums\Layout $layout
     */

    use VanOns\FilamentAttachmentLibrary\Enums\Layout;
    use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
@endphp

@props(['layout', 'disableMimeFilter' => false])

<div class="flex flex-col gap-4 justify-end mb-2 flex-wrap mt-2 w-full md:hidden">

    {{-- Upload & create directory --}}
    <div class="flex gap-x-2 w-full">
        <x-filament::button icon="heroicon-o-arrow-up-tray" class="flex-1" x-on:click="openFileDialog()">
            {{ __('filament-attachment-library::views.actions.attachment.upload') }}
        </x-filament::button>

        <x-filament::button color="gray" icon="heroicon-o-folder-plus" class="flex-1" wire:click="mountAction('createDirectory')">
            {{ __('filament-attachment-library::views.actions.directory.create') }}
        </x-filament::button>
    </div>

    {{-- Search --}}
    <x-filament::input.wrapper>
        <x-filament::input
            type="text"
            wire:model.live.debounce="search"
            placeholder="{{ __('filament-attachment-library::views.search') }}"
            class="w-full"
        />
    </x-filament::input.wrapper>

    <div class="flex justify-between gap-x-2 w-full">

        {{-- Sort --}}
        <x-filament::input.wrapper>
            <x-filament::input.select wire:model.live="sortBy">

                @foreach(AttachmentBrowser::SORTABLE_FIELDS as $field)
                    <option value="{{$field}}_asc">{{ __("filament-attachment-library::views.header_actions.sort.{$field}_ascending") }}</option>
                    <option value="{{$field}}_desc">{{ __("filament-attachment-library::views.header_actions.sort.{$field}_descending") }}</option>
                @endforeach

            </x-filament::input.select>
        </x-filament::input.wrapper>

        {{-- Mime-type filter --}}
        @if(!$disableMimeFilter)
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="mime">

                    @foreach(AttachmentBrowser::FILTERABLE_FILE_TYPES as $type => $mime)
                        <option value="{{$mime}}">{{__("filament-attachment-library::views.sidebar.mime_type.{$type}")}}</option>
                    @endforeach

                </x-filament::input.select>
            </x-filament::input.wrapper>
        @endif

        {{-- Page size --}}
        <x-filament::input.wrapper>
            <x-filament::input.select wire:model.live="pageSize">

                @foreach(AttachmentBrowser::PAGE_SIZES as $size)
                    <option value="{{$size}}">{{$size}}</option>
                @endforeach

            </x-filament::input.select>
        </x-filament::input.wrapper>

        {{-- Layout --}}
        <div class="flex flex-row items-center">
            <x-filament::icon-button
                icon="heroicon-m-squares-2x2"
                wire:click="$set('layout', '{{ Layout::GRID->value }}')"
                tooltip="{{ Layout::GRID->label() }}"
                @class([
                    'max-sm:m-0',
                    'border border-custom-600 dark:border-custom-400' => $layout->isGrid(),
                ])
            />

            <x-filament::icon-button
                icon="heroicon-m-queue-list"
                wire:click="$set('layout', '{{ Layout::LIST->value }}')"
                tooltip="{{ Layout::LIST->label() }}"
                @class([
                    'max-sm:m-0',
                    'border border-custom-600 dark:border-custom-400' => $layout->isList(),
                ])
            />
        </div>

    </div>

</div>
