<div class="text-center mx-auto">
    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-10 h-10 mx-auto"/>

    <h2 class="break-words text-lg font-medium text-gray-900 dark:text-gray-100">
        <span class="break-words">Geen bestanden of mappen gevonden</span>
    </h2>

    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
        Upload een nieuw bestand of navigeer naar een ander pad.
    </p>

    {{-- Offer user path back to main directory--}}
    @if($currentPath !== null)
    <x-filament::button
        wire:click="openPath(null)"
        class="mt-2"
        icon="heroicon-o-arrow-uturn-left"
    >
        Terug naar hoofdmap
    </x-filament::button>
    @endif
</div>
