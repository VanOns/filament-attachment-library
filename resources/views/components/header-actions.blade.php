<div class="flex gap-4 justify-end mb-2 flex-wrap mt-2 md:mt-0 min-w-full md:min-w-[initial]">

    {{-- Search --}}
    <x-filament::input.wrapper>
        <x-filament::input
            type="text"
            wire:model.live="search"
            placeholder="{{ __('filament-attachment-library::views.search') }}"
        />
    </x-filament::input.wrapper>

    {{-- Sort --}}
    <x-filament::input.wrapper>
        <x-filament::input.select wire:model.live="sortBy">

            @foreach(\VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser::SORTABLE_FIELDS as $field)
                <option value="{{$field}}_ascending">{{ __("filament-attachment-library::views.header_actions.sort.{$field}_ascending") }}</option>
                <option value="{{$field}}_descending">{{ __("filament-attachment-library::views.header_actions.sort.{$field}_descending") }}</option>
            @endforeach

        </x-filament::input.select>
    </x-filament::input.wrapper>

    {{-- Page size --}}
    <x-filament::input.wrapper>
        <x-filament::input.select wire:model.live="pageSize">

            @foreach(\VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser::PAGE_SIZES as $size)
                <option value="{{$size}}">{{$size}}</option>
            @endforeach

        </x-filament::input.select>
    </x-filament::input.wrapper>

</div>
