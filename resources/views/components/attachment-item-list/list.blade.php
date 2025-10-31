@props([
    'attachments',
    'selected',
    'inModal' => false,
])

@php
    /**
     * @var \Illuminate\Support\Collection<\VanOns\LaravelAttachmentLibrary\Models\Attachment> $attachments
     */
@endphp

<div class="grid gap-4">
    @foreach($attachments as $viewModel)
        <div
            @class([
                'flex flex-row items-center relative h-16 w-full p-2 transition ease-in-out box-border group',
                'rounded-xl shadow-sm border border-black/10 dark:border-white/10 bg-white dark:bg-gray-900',
                'hover:bg-gray-100 hover:dark:bg-gray-800',
                'ring-2 ring-primary-500' => $viewModel->isAttachment() && $viewModel->isSelected($selected)
            ])
        >
            <button
                type="button"
                class="text-left flex-1 flex flex-row items-center justify-between"
                @if($viewModel->isAttachment())
                    wire:click="selectAttachment({{ json_encode($viewModel->attachment->id) }})"
                @endif
                @if($viewModel->isDirectory())
                    wire:click="openPath('{{ $viewModel->directory->fullPath }}')"
                @endif
            >
                <div class="flex items-center gap-x-3">
                    {{-- Preview image if attachment is image --}}
                    @if($viewModel->isImage())
                        <img
                            alt="{{ $viewModel->attachment->alt }}"
                            loading="lazy"
                            src="{{ $viewModel->thumbnailUrl() }}"
                            class="relative rounded-lg overflow-hidden h-12 w-12 object-center object-cover ring-1 ring-gray-950/10 dark:ring-white/10"
                        >
                    @else
                        <div class="w-12 flex justify-center items-center">
                            @if($viewModel->isVideo())
                                <x-filament::icon icon="heroicon-o-film" class="size-8"/>
                            @endif

                            @if($viewModel->isDocument())
                                <x-filament::icon icon="heroicon-o-document" class="size-8"/>
                            @endif

                            @if($viewModel->isDirectory())
                                <x-filament::icon icon="heroicon-o-folder" class="size-8"/>
                            @endif
                        </div>
                    @endif

                    <div class="flex flex-col">
                        <p class="overflow-hidden box line-clamp-1">
                            @if($viewModel->isAttachment())
                                {{ $viewModel->attachment->name }}
                            @endif

                            @if($viewModel->isDirectory())
                                {{ $viewModel->directory->name }}
                            @endif
                        </p>

                        @if($viewModel->isAttachment())
                            <p class="block text-sm font-medium opacity-60">{{ round($viewModel->attachment->size / 1024 / 1024) }} MB</p>
                        @endif
                    </div>
                </div>
            </button>

            <x-filament-attachment-library::attachment-item-list.attachment-actions :$viewModel />
        </div>
    @endforeach
</div>
