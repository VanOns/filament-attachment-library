@props([ 'currentPath' => null ])

<div class="text-center text-center border-2 border-dashed px-4 py-8 rounded-xl w-full border-gray-400">

    <h2 class="break-words text-lg font-medium text-gray-900 dark:text-gray-100">
        <span class="break-words">{{ __('filament-attachment-library::views.browser.empty.title') }}</span>
    </h2>

    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
        {{ __('filament-attachment-library::views.browser.empty.description') }}
    </p>

    @if($currentPath)
        <x-filament::button
                wire:click="$dispatch('open-path', {path: null})"
                class="mt-12"
                icon="heroicon-o-arrow-uturn-left"
        >
            {{ __('filament-attachment-library::views.browser.empty.button') }}
        </x-filament::button>
    @endif
</div>
