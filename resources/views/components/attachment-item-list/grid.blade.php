@php
    /**
     * @var \Illuminate\Support\Collection<\VanOns\LaravelAttachmentLibrary\Models\Attachment> $attachments
     */
@endphp

@props([ 'attachments', 'selected' ])

<div class="grid lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
    @foreach($attachments as $viewModel)
        <div
            @class([
                'cursor-pointer relative transition ease-in-out box-border group aspect-square',
                'rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10',
                $viewModel->isAttachment() && $viewModel->isSelected($selected)
                    ? 'bg-black dark:bg-gray-300 dark:text-black text-white hover:dark:bg-red-400 hover:dark:text-white'
                    : 'dark:bg-gray-900 hover:bg-gray-900 hover:dark:bg-gray-300 hover:text-white hover:dark:text-black'
            ])
        >
            <button
                type="button"
                class="text-left flex flex-col w-full h-full"
                @if($viewModel->isAttachment())
                    wire:click="selectAttachment({{ json_encode($viewModel->attachment->id) }})"
                @endif
                @if($viewModel->isDirectory())
                    wire:click="openPath('{{ $viewModel->directory->fullPath }}')"
                @endif
            >
                @if($viewModel->isImage())
                    <img
                            alt="a{{ $viewModel->attachment->alt }}"
                            loading="lazy"
                            width="auto"
                            height="100%"
                            src="{{ $viewModel->thumbnailUrl() }}"
                            class="relative rounded-lg overflow-hidden h-full w-full object-center opacity-60 object-cover"
                    >
                @endif

                {{-- Attachment item icon --}}
                <div class="absolute p-6">
                    @if($viewModel->isDirectory())
                        <x-filament::icon icon="heroicon-o-folder" class="w-8 h-8 m-0"/>
                    @endif

                    @if($viewModel->isImage())
                        <x-filament::icon icon="heroicon-o-photo" class="w-8 h-8 m-0"/>
                    @endif

                    @if($viewModel->isVideo())
                        <x-filament::icon icon="heroicon-o-film" class="w-8 h-8 m-0"/>
                    @endif

                    @if($viewModel->isDocument())
                        <x-filament::icon icon="heroicon-o-document" class="w-8 h-8 m-0"/>
                    @endif

                    <p class="max-w-48 overflow-hidden box line-clamp-1 mt-2">
                        @if($viewModel->isAttachment())
                            {{ $viewModel->attachment->name }}
                        @endif

                        @if($viewModel->isDirectory())
                            {{ $viewModel->directory->name }}
                        @endif
                    </p>

                    @if($viewModel->isAttachment())
                        <p class="block text-sm font-medium">
                            {{ round($viewModel->attachment->size / 1024 / 1024, 2) }} MB
                        </p>
                    @endif
                </div>
            </button>
            <x-filament-attachment-library::attachment-item-list.attachment-actions class="absolute top-4 right-4" :$viewModel />
        </div>
    @endforeach
</div>
