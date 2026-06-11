@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
@endphp

<div @class([$class])>
    <x-filament::section :$contained>
        {{-- Scrolling happens inside the card so its shadow/ring is never clipped by the scrollport --}}
        <div class="md:max-h-[calc(100vh-12rem)] md:overflow-y-auto md:-m-2 md:p-2">
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
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $attachment->extension }} — {{ $attachment->size }} MB</p>

                    <hr class="mt-3 border-gray-200 dark:border-white/10">

                    <div class="grid mt-3 grid-cols-auto gap-y-2 md:grid-cols-2">
                        <p class="flex-1 text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.path') }}</p>
                        <p class="flex-1">/{{ ltrim($attachment->path ?? '', '/') }}</p>

                        <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.date.created_at') }}</p>
                        <p>{{ $attachment->createdAt?->translatedFormat('d F Y H:i') ?: '-' }}</p>

                        <button
                            class="col-span-2 flex items-center gap-2 w-full text-left text-sm rounded-lg px-3 py-2 bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 border border-gray-200 dark:border-white/10 transition group"
                            x-data="{ url: @js($attachment->url) }"
                            x-on:click="navigator.clipboard.writeText(url).then(
                                () => new FilamentNotification()
                                    .title(window.filamentData?.fal?.labels?.clipboardSuccess)
                                    .success()
                                    .send()
                            )"
                        >
                            <span class="flex-1 break-all text-gray-700 dark:text-gray-300">{{ $attachment->url }}</span>
                            <x-filament::icon icon="heroicon-o-document-duplicate" class="w-4 h-4 shrink-0 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-200 transition" />
                        </button>
                    </div>

                    {{-- Actions --}}
                    <div class="grid grid-cols-2 gap-2 mt-4">
                        <x-filament::button color="gray" icon="heroicon-o-arrow-top-right-on-square" class="relative [&>.fi-icon]:absolute [&>.fi-icon]:start-2 [&>.fi-icon]:top-1/2 [&>.fi-icon]:-translate-y-1/2" tag="a" :href="$attachment->url" target="_blank">
                            {{ __('filament-attachment-library::views.actions.attachment.open') }}
                        </x-filament::button>

                        <x-filament::button color="gray" icon="heroicon-o-pencil-square" class="relative [&>.fi-icon]:absolute [&>.fi-icon]:start-2 [&>.fi-icon]:top-1/2 [&>.fi-icon]:-translate-y-1/2"
                            x-on:click="$dispatch('mount-action', { name: 'editAttachmentAction', arguments: { attachment_id: {{ json_encode($attachment->id) }} } })"
                        >
                            {{ __('filament-attachment-library::views.actions.attachment.edit') }}
                        </x-filament::button>

                        <x-filament::button color="gray" icon="heroicon-o-arrow-right-circle" class="relative [&>.fi-icon]:absolute [&>.fi-icon]:start-2 [&>.fi-icon]:top-1/2 [&>.fi-icon]:-translate-y-1/2"
                            x-on:click="$dispatch('mount-action', { name: 'moveAttachmentAction', arguments: { attachment_id: {{ json_encode($attachment->id) }} } })"
                        >
                            {{ __('filament-attachment-library::views.actions.attachment.move') }}
                        </x-filament::button>

                        <x-filament::button color="gray" icon="heroicon-o-arrow-path" class="relative [&>.fi-icon]:absolute [&>.fi-icon]:start-2 [&>.fi-icon]:top-1/2 [&>.fi-icon]:-translate-y-1/2"
                            x-on:click="$dispatch('mount-action', { name: 'replaceAttachmentAction', arguments: { attachment_id: {{ json_encode($attachment->id) }} } })"
                        >
                            {{ __('filament-attachment-library::views.actions.attachment.replace') }}
                        </x-filament::button>

                        <x-filament::button color="danger" icon="heroicon-o-trash" class="col-span-2 relative [&>.fi-icon]:absolute [&>.fi-icon]:start-2 [&>.fi-icon]:top-1/2 [&>.fi-icon]:-translate-y-1/2"
                            x-on:click="$dispatch('mount-action', { name: 'deleteAttachment', arguments: { attachment_id: {{ json_encode($attachment->id) }} } })"
                        >
                            {{ __('filament-attachment-library::views.actions.attachment.delete') }}
                        </x-filament::button>
                    </div>

                    {{-- Secondary details (meta, dates, image technicals) behind a lightweight disclosure --}}
                    <div class="border-t border-gray-200 dark:border-white/10 pt-3 mt-4 -mb-2" x-data="{ open: false }">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition"
                            x-on:click="open = !open"
                        >
                            {{ __('filament-attachment-library::views.info.details.sections.more') }}
                            <x-filament::icon icon="heroicon-m-chevron-down" class="size-4 transition-transform" x-bind:class="{ '-rotate-180': open }" />
                        </button>

                        <div x-show="open" x-cloak>
                            {{-- Meta fields --}}
                            <div class="grid mt-3 grid-cols-2 gap-y-2">
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
                            </div>

                            {{-- Date fields --}}
                            <div class="grid mt-3 pt-3 border-t border-gray-200 dark:border-white/10 grid-cols-2 gap-y-2">
                                <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.date.created_by') }}</p>
                                <p>{{ $attachment->createdBy ?: '-' }}</p>

                                <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.date.created_at') }}</p>
                                <p>{{ $attachment->createdAt?->translatedFormat('d F Y H:i') ?: '-' }}</p>

                                <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.date.updated_by') }}</p>
                                <p>{{ $attachment->updatedBy ?: '-' }}</p>

                                <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.date.updated_at') }}</p>
                                <p>{{ $attachment->updatedAt?->translatedFormat('d F Y H:i') ?: '-'}}</p>
                            </div>

                            {{-- Technical fields --}}
                            <div class="grid mt-3 pt-3 border-t border-gray-200 dark:border-white/10 grid-cols-2 gap-y-2">
                                <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.mime_type') }}</p>
                                <p>{{ $attachment->mimeType }}</p>

                                @if($attachment->isImage())
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.image.dimensions') }}</p>
                                    <p>{{ $attachment->dimensions }}</p>

                                    <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.image.channels') }}</p>
                                    <p>{{ $attachment->channels }}</p>

                                    <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.sections.image.bits') }}</p>
                                    <p>{{ $attachment->bits }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif
        </div>
    </x-filament::section>
    <x-filament-actions::modals/>
</div>
