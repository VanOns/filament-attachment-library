@props(['title', 'subtitle' => null])

@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

@props(['tag' => 'div', 'selected' => false])

<div class="relative group">
    <button
        type="button"
        {{ $attributes->class([
            'w-full text-left bg-gray-100 dark:bg-gray-800 overflow-hidden rounded-xl border shadow-xs border-black/10 dark:border-white/10',
            'ring-3 ring-primary-500' => $selected
        ]) }}
    >
        <div class="aspect-[4/3] w-full overflow-hidden flex justify-center items-center">
            {{ $slot }}
        </div>

        <div class="p-2 bg-white dark:bg-gray-900 group-hover:bg-gray-100 dark:group-hover:bg-black border-t border-black/10 dark:border-white/10 text-sm transition">
            <p class="font-semibold line-clamp-1">{{ $title }}</p>
            @if($subtitle)
                <p class="opacity-60">{{ $subtitle }}</p>
            @endif
        </div>
    </button>
    @isset($actions)
        <div class="absolute top-2 right-2">
            {{ $actions }}
        </div>
    @endisset
</div>

