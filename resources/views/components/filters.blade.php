<div class="flex gap-4 flex-wrap">
    <!-- Search -->
    <x-filament::input.wrapper class="flex-1 min-w-full md:min-w-[initial]">
        <x-filament::input type="text" wire:model.live="search" placeholder="Zoeken.." />
    </x-filament::input.wrapper>

    <!-- Sort by -->
    <x-filament::input.wrapper class="flex-1 min-w-full md:min-w-[initial]">
        <x-filament::input.select wire:model.live="sortBy">
            <option value="created_at_ascending">Uploaddatum oplopend</option>
            <option value="created_at_descending">Uploaddatum aflopend</option>
            <option value="name_ascending">Naam oplopend</option>
            <option value="name_descending">Naam aflopend</option>
        </x-filament::input.select>
    </x-filament::input.wrapper>

    <!-- Page size -->
    <x-filament::input.wrapper class="flex-1 min-w-full md:min-w-[initial]">
        <x-filament::input.select wire:model.live="pageSize">
            <option value="5">5</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </x-filament::input.select>
    </x-filament::input.wrapper>
</div>
