@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

@props(['title', 'subtitle', 'selected' => false])

<div
    @class([
        'flex flex-row items-center relative h-16 w-full p-2 transition ease-in-out box-border group',
        'rounded-xl shadow border border-black/10 dark:border-white/10 bg-white dark:bg-gray-900',
        'hover:bg-gray-100 hover:dark:bg-gray-800',
        'ring-2 ring-primary-500' => $selected
    ])
>
    <button
        type="button"
        class="text-left flex-1 flex flex-row items-center justify-between"
        {{ $attributes }}
    >
        <div class="flex items-center gap-x-3">
            <div class="size-12 rounded-lg flex justify-center items-center overflow-hidden ring-1 ring-gray-950/10 dark:ring-white/10 bg-gray-100 dark:bg-gray-800">
                {{ $slot }}
            </div>

            <div class="flex flex-col text-sm">
                <p class="font-semibold line-clamp-1">
                    {{ $title }}
                </p>

                @if($subtitle)
                    <p class="block text-sm font-medium opacity-60">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    </button>

    @isset($actions)
        {{ $actions }}
    @endisset
</div>
