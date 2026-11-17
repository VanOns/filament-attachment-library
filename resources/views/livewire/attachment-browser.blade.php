@php
    use VanOns\FilamentAttachmentLibrary\Enums\Layout;
@endphp

<div
    class="relative"
    x-data="attachmentBrowser({
        maxBytes: @js(\VanOns\FilamentAttachmentLibrary\Support\TemporaryUploadLimit::bytes()),
        mime: @js($mime),
        extensions: @js($extensions),
        wireDisabled: true,
        nestedUploader: false,
        measureOverlay: true,
        messages: @js([
            'tooLarge' => __('filament-attachment-library::notifications.attachment.upload_failed_too_large', ['max' => \VanOns\FilamentAttachmentLibrary\Support\TemporaryUploadLimit::label()]),
            'wrongType' => __('filament-attachment-library::notifications.attachment.upload_failed_wrong_type'),
            'failed' => __('filament-attachment-library::notifications.attachment.upload_failed'),
        ]),
    })"
    x-on:dragenter.prevent="onDragEnter($event)"
    x-on:scroll.window="updateOverlayHeight()"
    x-on:resize.window="updateOverlayHeight()"
    x-on:dragover.prevent
    x-on:dragleave.prevent="onDragLeave()"
    x-on:drop.prevent="handleDrop($event)"
>
    {{-- Target of the upload buttons: opens the OS file explorer, files go through the drop pipeline --}}
    <input type="file" multiple class="hidden" x-ref="fileInput" x-on:change="onFilesChosen($event)">

    <div class="flex justify-between align-center mb-6 items-center flex-wrap">
        <x-filament-attachment-library::breadcrumbs/>
        <x-filament-attachment-library::header-actions :$layout :$disableMimeFilter :$searchPlaceholder/>
        <x-filament-attachment-library::header-actions-mobile :$layout :$disableMimeFilter :$searchPlaceholder :tree-navigation="(bool) $tree"/>
    </div>

    <div wire:key="attachment-search-heading">
        @if($search)
            <h1>{{ __('filament-attachment-library::views.browser.search_results') }} <span>{{ $search }}</span></h1>
        @endif
    </div>

    <div class="flex flex-col gap-6 mt-4 flex-wrap md:flex-row">
        {{-- A container, so the tree goes by the room it has here, whatever the window, navigation or modal around it --}}
        <div @class(['flex-1 min-w-0 order-2 md:order-1', '@container' => $tree])>
            <div class="flex flex-col gap-6 @2xl:flex-row">
                @if($tree)
                    <x-filament-attachment-library::directory.tree :$tree :collapsed="$treeCollapsed"/>
                @endif

                <div
                    @class([
                        'flex-1 min-w-0 transition-opacity',
                        'opacity-50 pointer-events-none' => $disabled,
                    ])
                    {{-- Opening a directory takes a moment: fade what is about to be replaced --}}
                    wire:loading.class="opacity-50"
                    wire:target="openPath, openDirectory"
                >
                    @if(! $tree && ! $directories->isEmpty())
                        <x-filament-attachment-library::items.container :layout="$layout">
                            @foreach($directories as $directory)
                                <div
                                    wire:key="directory-browser-item-{{ md5($directory->fullPath) }}"
                                    class="rounded-xl"
                                    data-directory="{{ $this->relativePath($directory->fullPath) }}"
                                    x-bind:class="{ 'ring-2 ring-primary-500': dropPath === $el.dataset.directory }"
                                    x-on:dragover="onMoveOver($event, $el.dataset.directory)"
                                    x-on:dragleave="onMoveLeave($event, $el.dataset.directory)"
                                    x-on:drop="onMoveDrop($event, $el.dataset.directory)"
                                >
                                    <x-filament-attachment-library::directory.browser-item
                                        :$directory
                                        :layout="$layout"
                                    />
                                </div>
                            @endforeach
                        </x-filament-attachment-library::items.container>

                        <div class="w-full border-t border-gray-300 dark:border-gray-700 my-6"></div>
                    @endif

                    @if(!$attachments->isEmpty())
                        <div
                            x-data="attachmentSelection({
                                state: $wire.entangle('selected'),
                                dragLabel: @js(trans_choice('filament-attachment-library::views.browser.file_count', 2, ['count' => ':count'])),
                            })"
                            x-on:dragend="onDragEnd()"
                        >
                            <x-filament-attachment-library::items.container :layout="$layout">
                                @foreach($attachments as $attachment)
                                    <div
                                        wire:key="attachment-browser-item-{{ $attachment->id }}"
                                        draggable="true"
                                        data-attachment="{{ $attachment->id }}"
                                        data-filename="{{ $attachment->filename }}"
                                        x-on:dragstart="onDragStart($event, {{ json_encode($attachment->id) }})"
                                    >
                                        <x-filament-attachment-library::attachment.browser-item
                                            :$attachment
                                            :layout="$layout"
                                        />
                                    </div>
                                @endforeach
                            </x-filament-attachment-library::items.container>

                            {{-- What follows the pointer while dragging; off screen until then --}}
                            <div
                                x-ref="dragGhost"
                                aria-hidden="true"
                                class="pointer-events-none fixed -left-[200vw] top-0 flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-medium text-gray-950 shadow-lg ring-1 ring-gray-950/10 dark:bg-gray-800 dark:text-white dark:ring-white/10"
                            >
                                <x-filament::icon icon="heroicon-m-document" class="size-4 shrink-0 text-gray-400"/>
                                <span x-ref="dragGhostLabel" class="max-w-64 truncate"></span>
                            </div>
                        </div>
                    @endif

                    @if($attachments->isEmpty() && $directories->isEmpty())
                        <x-filament-attachment-library::empty-path-notice :$currentPath/>
                    @elseif($tree && $attachments->isEmpty())
                        {{-- The tree shows the directories in here, so say that it holds no files --}}
                        <x-filament-attachment-library::empty-path-notice
                            :$currentPath
                            :title="$search ? null : __('filament-attachment-library::views.browser.empty.title_files')"
                        />
                    @endif
                </div>
            </div>
        </div>

        <x-filament-attachment-library::sidebar :$selected :$currentPath :$scope :tree-navigation="(bool) $tree" class="order-1 md:order-2"/>

        <div class="mt-4 w-full order-3">
            <x-filament::pagination :paginator="$attachments" extreme-links/>
        </div>
    </div>

    <x-filament-actions::modals/>

    {{-- Drop overlay: bounded to the component, with a sticky viewport-capped box inside so the
         visible part always fits the window, no matter how long the listing is --}}
    <div
        x-cloak
        x-show="dragDepth > 0 || uploading"
        class="absolute inset-y-0 -inset-x-4 z-10"
    >
        <div x-ref="dropBox" class="sticky top-4 flex max-h-full flex-col items-center justify-center gap-4 rounded-xl border-2 border-dashed border-primary-500 bg-white/85 backdrop-blur-sm dark:bg-gray-900/85">
            <div class="rounded-full bg-primary-100 p-5 dark:bg-primary-500/20">
                <x-filament::icon icon="heroicon-o-arrow-up-tray" class="h-10 w-10 text-primary-600 dark:text-primary-400"/>
            </div>

            <span x-show="! uploading" class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('filament-attachment-library::views.browser.drop.prompt') }}
            </span>

            <span x-show="uploading" class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('filament-attachment-library::views.browser.drop.uploading') }} <span x-text="progress + '%'"></span>
            </span>
        </div>
    </div>
</div>
