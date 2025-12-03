@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

<div @class([$class])>
    <x-filament::section :$contained>
        @if(!$attachment)
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    <span class="break-words">{{ __('filament-attachment-library::views.info.empty.title') }}</span>
                </h2>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ __('filament-attachment-library::views.info.empty.description') }}
                </p>
            </div>
        @else
            <div>
                @if($attachment->isImage())
                    <img
                        alt="{{ $attachment->alt }}"
                        loading="lazy"
                        src="{{ $attachment->thumbnailUrl() }}"
                        class="relative rounded-lg dark:opacity-80 focus-within:ring-2 focus-within:ring-offset-4 focus-within:ring-offset-gray-100 focus-within:ring-primary-600 h-full w-auto max-h-48 m-auto"
                    >
                @endif

                @if($attachment->isVideo())
                    <video
                        src="{{ $attachment->url }}"
                        controls
                        class="relative object-cover object-center rounded-lg dark:opacity-80 focus-within:ring-2 focus-within:ring-offset-4 focus-within:ring-offset-gray-100 focus-within:ring-primary-600 h-full w-full max-h-48"
                    ></video>
                @endif


                @if($attachment->isDocument())
                    <x-filament::icon icon="heroicon-o-document" class="w-8 h-8" />
                @endif

                {{-- Details --}}
                <div class="mt-6">
                    <h2 class="break-words text-xl font-medium text-gray-900 dark:text-gray-100">{{ $attachment->name }}</h2>
                    <div class="grid mt-2 grid-cols-auto gap-y-2 md:grid-cols-2">
                        <p class="flex-1 text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.path') }}</p>
                        <p class="flex-1">{{ $attachment->path }}</p>

                        <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.mime_type') }}</p>
                        <p>{{ $attachment->mimeType }}</p>

                        <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.size') }}</p>
                        <p>{{ $attachment->size }} MB</p>

                        <p class="flex-1 text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.url') }}</p>
                        <p class="cursor-pointer break-all" x-clipboard="{{ $attachment->url }}">
                            <span>{{ $attachment->url }}</span>
                            <x-filament::icon icon="heroicon-o-document-duplicate" class="w-6 h-6 inline" />
                        </p>
                    </div>

                    {{-- Date fields --}}
                    <x-filament::section collapsible collapsed class="mt-4">
                        <x-slot name="heading">
                            {{ __('filament-attachment-library::views.info.details.sections.date.header') }}
                        </x-slot>

                        <div class="grid mt-2 grid-cols-2 gap-y-2">
                            <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.date.created_by') }}</p>
                            <p>{{ $attachment->createdBy ?: '-' }}</p>

                            <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.date.created_at') }}</p>
                            <p>{{ $attachment->createdAt?->translatedFormat('d F Y') ?: '-' }}</p>

                            <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.date.updated_by') }}</p>
                            <p>{{ $attachment->updatedBy ?: '-' }}</p>

                            <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.date.updated_at') }}</p>
                            <p>{{ $attachment->updatedAt?->translatedFormat('d F Y') ?: '-'}}</p>
                        </div>
                    </x-filament::section>

                    @if($attachment->isImage())
                        <x-filament::section collapsible collapsed class="mt-4">
                            <x-slot name="heading">
                                {{ __('filament-attachment-library::views.info.details.sections.image.header') }}
                            </x-slot>

                            <div class="grid mt-2 grid-cols-2 gap-y-2">
                                <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.image.dimensions') }}</p>
                                <p>{{ $attachment->dimensions }}</p>

                                <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.image.channels') }}</p>
                                <p>{{ $attachment->channels }}</p>

                                <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.image.bits') }}</p>
                                <p>{{ $attachment->bits }}</p>
                            </div>
                        </x-filament::section>
                    @endif

                    {{-- Meta fields --}}
                    <x-filament::section collapsible collapsed class="mt-4">
                        <x-slot name="heading">
                            {{ __('filament-attachment-library::views.info.details.sections.meta.header') }}
                        </x-slot>

                        <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.meta.title') }}</p>
                        <p class="break-all">{{ $attachment->title ?: '' }}</p>

                        <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.meta.description') }}</p>
                        <p class="break-all">{{ $attachment->description ?: '' }}</p>


                        @if($attachment->isImage())
                            <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.meta.alt') }}</p>
                            <p class="break-all">{{ $attachment->alt ?: '' }}</p>

                            <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.meta.caption') }}</p>
                            <p class="break-all">{{ $attachment->caption ?: '' }}</p>
                        @endif
                    </x-filament::section>

                </div>

                <div class="mt-6">
                    <div class="grid grid-cols-1 gap-2 mt-2">
                        <x-filament::button color="gray" x-on:click="window.open(attachment.url)" tag="a" :href="$attachment->url" target="_blank">
                            {{ __('filament-attachment-library::views.actions.attachment.open') }}
                        </x-filament::button>

                        <x-filament::button color="gray"
                            wire:click="mountAction('editAttachmentAction', { attachment_id: {{ json_encode($attachment->id) }}})"
                        >
                            {{ __('filament-attachment-library::views.actions.attachment.edit') }}
                        </x-filament::button>

                        <x-filament::button color="gray"
                            wire:click="mountAction('moveAttachmentAction', { attachment_id: {{ json_encode($attachment->id) }}})"
                        >
                            {{ __('filament-attachment-library::views.actions.attachment.move') }}
                        </x-filament::button>

                        <x-filament::button color="danger"
                            wire:click="mountAction('deleteAttachment', { attachment_id: {{ json_encode($attachment->id) }}})"
                        >
                            {{ __('filament-attachment-library::views.actions.attachment.delete') }}
                        </x-filament::button>
                    </div>
                </div>
            </div>
        @endif
    </x-filament::section>
    <x-filament-actions::modals/>
</div>
