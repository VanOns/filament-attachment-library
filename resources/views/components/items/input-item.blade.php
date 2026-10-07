@props(['attachment' => null, 'placeholder' => null, 'reorderable' => false, 'disabled' => false, 'valid' => true])

@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel|null $attachment
     */
    $thumbnailUrl = match (true) {
        $attachment?->isImage() => $attachment->thumbnailUrl(),
        $attachment?->isVideo() => $attachment->posterUrl(),
        default => null,
    };
@endphp

<x-filament::input.wrapper :disabled="$disabled" :valid="$valid" {{ $attributes->class(['overflow-hidden']) }}>
    <div class="flex min-h-9 items-center gap-2 pe-2">
        @if($attachment && $reorderable && ! $disabled)
            <button
                data-drag-handle
                type="button"
                class="cursor-grab ps-3 text-gray-400 transition hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                aria-label="{{ __('filament-attachment-library::views.field.drag_to_reorder') }}"
            >
                <x-filament::icon icon="heroicon-o-bars-2" class="size-4"/>
            </button>
        @endif

        <button
            type="button"
            @disabled($disabled)
            x-on:click="openBrowser({{ json_encode($attachment?->id) }})"
            @class([
                'flex min-w-0 flex-1 items-center gap-2 py-1.5 text-start text-sm',
                'ps-3' => ! ($attachment && $reorderable && ! $disabled),
                'cursor-pointer' => ! $disabled,
            ])
        >
            @if($attachment)
                @if($thumbnailUrl)
                    <img src="{{ $thumbnailUrl }}" alt="" class="size-5 shrink-0 rounded object-cover ring-1 ring-gray-950/10 dark:ring-white/10">
                @else
                    <x-filament::icon :icon="$attachment->icon()" class="size-5 shrink-0 text-gray-400 dark:text-gray-500"/>
                @endif

                <span class="min-w-0 flex-1 truncate text-gray-950 dark:text-white" title="{{ $attachment->filename }}">{{ $attachment->name }}</span>

                <x-filament::badge color="gray" size="sm" class="shrink-0">{{ $attachment->extension }}</x-filament::badge>
            @else
                <x-filament::icon icon="heroicon-o-folder-open" class="size-5 shrink-0 text-gray-400 dark:text-gray-500"/>

                <span class="truncate text-gray-400 dark:text-gray-500">{{ $placeholder }}</span>
            @endif
        </button>

        @if($attachment && ! $disabled)
            <button
                type="button"
                class="shrink-0 rounded p-1 text-gray-400 transition hover:text-danger-600 dark:text-gray-500 dark:hover:text-danger-400"
                aria-label="{{ __('filament-attachment-library::forms.attachment_field.remove') }}"
                x-on:click="$dispatch('attachment-removed', { id: {{ json_encode($attachment->id) }} })"
            >
                <x-filament::icon icon="heroicon-o-x-mark" class="size-4"/>
            </button>
        @endif
    </div>
</x-filament::input.wrapper>
