<div class="flex flex-col gap-4 justify-end mb-2 flex-wrap mt-2 w-full md:hidden">

    {{-- Search --}}
    <x-filament::input.wrapper>
        <x-filament::input
            type="text"
            wire:model.live="search"
            placeholder="{{ __('filament-attachment-library::views.search') }}"
            class="w-full"
        />
    </x-filament::input.wrapper>

    <div class="flex justify-between gap-x-2 w-full">

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

        {{-- Layout --}}
        <div class="flex flex-row items-center">
            <x-filament::icon-button
                icon="heroicon-m-squares-2x2"
                wire:click="$set('layout', 'grid')"
                tooltip="{{ __('filament-attachment-library::views.header_actions.layout.grid') }}"
                @class([
                    'max-sm:m-0',
                    'border border-custom-600 dark:border-custom-400' => $layout === 'grid'
                ])
            />

            <x-filament::icon-button
                icon="heroicon-m-queue-list"
                wire:click="$set('layout', 'list')"
                tooltip="{{ __('filament-attachment-library::views.header_actions.layout.list') }}"
                @class([
                    'max-sm:m-0',
                    'border border-custom-600 dark:border-custom-400' => $layout === 'list'
                ])
            />
        </div>

    </div>

</div>
