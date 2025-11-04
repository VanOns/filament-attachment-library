@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

@props(['attachment', 'selected' => false])

<div class="relative group">
    <button
        @class([
            'w-full relative text-left aspect-square overflow-hidden rounded-xl bg-black/5 dark:bg-white/5 border shadow-xs border-black/10 dark:border-white/10',
            'ring-3 ring-primary-500' => $selected
        ])
        wire:click="selectAttachment({{ json_encode($attachment->id) }})"
        type="button"
    >
        <div class="absolute inset-0 group-hover:bg-black/50"></div>

        @if($attachment->isImage())
            <img
                alt="{{ $attachment->alt }}"
                loading="lazy"
                src="{{ $attachment->thumbnailUrl() }}"
                class="object-contain h-full w-full"
            >
        @endif

        <div @class([
            'absolute inset-0 p-4 transition flex flex-col justify-between group-hover:text-white',
            'opacity-0 group-hover:opacity-100' => $attachment->isImage(),
            '' => !$attachment->isImage(),
        ])>
            <div class="text-gray-500 group-hover:text-gray-200 transition flex items-center flex-1">
                @if($attachment->isVideo())
                    <x-filament::icon icon="heroicon-o-film" class="size-20" />
                @endif

                @if($attachment->isDocument())
                    <x-filament::icon icon="heroicon-o-document-text" class="size-20" />
                @endif
            </div>


            <div>
                <p class="font-bold">{{ \Illuminate\Support\Str::limit($attachment->filename, 100) }}</p>
                <p>{{ $attachment->size }} MB</p>
            </div>
        </div>

    </button>
    @isset($actions)
        <div class="absolute top-4 right-4">
            {{ $actions }}
        </div>
    @endisset
</div>
