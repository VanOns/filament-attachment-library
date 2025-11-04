@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\DirectoryViewModel $directory
     */
@endphp

@props(['directory'])

<div class="relative group">
    <button
        type="button"
        wire:click="openPath('{{ $directory->fullPath }}')"
        @class(['w-full text-left aspect-square flex justify-center items-center relative rounded-xl overflow-hidden group bg-black/5 dark:bg-white/5 border shadow-xs border-black/10 dark:border-white/10'])
    >
        <x-filament::icon icon="heroicon-o-folder" class="size-24 stroke-gray-500" />

        <div class="absolute inset-0 transition flex items-end group-hover:bg-black/50 bg-black/25">
            <div class="text-white p-4">
                <p class="font-bold">{{ \Illuminate\Support\Str::limit($directory->name, 100) }}</p>
            </div>
        </div>
    </button>
    @isset($actions)
        <div class="absolute top-4 right-4">
            {{ $actions }}
        </div>
    @endisset
</div>

