<div class="flex gap-4 justify-end mb-2 flex-wrap mt-2 md:mt-0">

    {{-- Filtering & sorting  --}}

    {{-- Search --}}
    <x-filament::input.wrapper class="flex-1 min-w-full md:min-w-[initial]">
        <x-filament::input type="text" wire:model.change="search" placeholder="{{ __('filament-attachment-library::views.search')  }}" />
    </x-filament::input.wrapper>

    <x-filament::dropdown placement="bottom-start">

        <x-slot name="trigger">
            <x-filament::button color="gray">
                <h1>Filters</h1>
            </x-filament::button>
        </x-slot>

        <x-filament::dropdown.list>

            {{-- Sort by --}}
            <x-filament::dropdown.list.item>
                <x-filament::input.wrapper class="flex-1 min-w-full md:min-w-[initial]">
                    <x-filament::input.select wire:model.live="sortBy">
                        <option value="created_at_ascending">{{ __('filament-attachment-library::views.header-actions.created_at_ascending')  }}</option>
                        <option value="created_at_descending">{{ __('filament-attachment-library::views.header-actions.created_at_descending')  }}</option>
                        <option value="name_ascending">{{ __('filament-attachment-library::views.header-actions.name_ascending')  }}</option>
                        <option value="name_descending">{{ __('filament-attachment-library::views.header-actions.name_descending')  }}</option>
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
        </x-filament::dropdown.list>

    </x-filament::dropdown>

    {{-- Attachment actions --}}
    <template x-if="$store.attachmentBrowser?.showActions()">
        <x-filament-actions::group
            :actions="[
                $this->uploadAttachmentAction,
                $this->createDirectoryAction,
            ]"
            label="{{ __('filament-attachment-library::views.header-actions.actions')  }}"
            :icon='false'
            :button='true'
            color="primary"
            size="md"
            dropdown-placement="bottom-start"
        />
    </template>

</div>
