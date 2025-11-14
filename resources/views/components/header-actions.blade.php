@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\Enums\Layout $layout
     */

    use VanOns\FilamentAttachmentLibrary\Enums\Layout;
    use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
@endphp

<div class="hidden flex-col gap-4 justify-end mb-2 flex-wrap mt-2 md:flex md:mt-0 min-w-full md:min-w-[initial] md:flex-row">

    {{-- Search --}}
    <x-filament::input.wrapper>
        <x-filament::input
            type="text"
            wire:model.live.debounce="search"
            placeholder="{{ __('filament-attachment-library::views.search') }}"
            class="w-full"
        />
    </x-filament::input.wrapper>

    {{-- Sort --}}
    <x-filament::input.wrapper>
        <x-filament::input.select wire:model.live="sortBy">

            @foreach(AttachmentBrowser::SORTABLE_FIELDS as $field)
                <option value="{{$field}}_asc">{{ __("filament-attachment-library::views.header_actions.sort.{$field}_ascending") }}</option>
                <option value="{{$field}}_desc">{{ __("filament-attachment-library::views.header_actions.sort.{$field}_descending") }}</option>
            @endforeach

        </x-filament::input.select>
    </x-filament::input.wrapper>

    {{-- Page size --}}
    <x-filament::input.wrapper>
        <x-filament::input.select wire:model.live="pageSize">

            @foreach(AttachmentBrowser::PAGE_SIZES as $size)
                <option value="{{$size}}">{{$size}}</option>
            @endforeach

        </x-filament::input.select>
    </x-filament::input.wrapper>

    {{-- Layout --}}
    <div class="flex flex-row gap-x-4 items-center">
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
