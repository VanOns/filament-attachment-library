@php
    use VanOns\FilamentAttachmentLibrary\Enums\Layout
@endphp

<div
    class="relative"
    x-data="attachmentDropZone({
        maxBytes: @js(\VanOns\FilamentAttachmentLibrary\Support\TemporaryUploadLimit::bytes()),
        mime: null,
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
        <x-filament-attachment-library::header-actions :$layout :$disableMimeFilter/>
        <x-filament-attachment-library::header-actions-mobile :$layout :$disableMimeFilter/>
    </div>

    <div wire:key="attachment-search-heading">
        @if($search)
            <h1>{{ __('filament-attachment-library::views.browser.search_results') }} <span>{{ $search }}</span></h1>
        @endif
    </div>

    <div class="flex flex-col gap-6 mt-4 flex-wrap md:flex-row">
        <div
            @class([
                'flex-1 order-2 md:order-1',
                'opacity-50 pointer-events-none' => $disabled,
            ])
        >
            @if(!$directories->isEmpty())
                <x-filament-attachment-library::items.container :layout="$layout">
                    @foreach($directories as $directory)
                        <div wire:key="directory-browser-item-{{ md5($directory->fullPath) }}">
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
                <x-filament-attachment-library::items.container :layout="$layout">
                    @foreach($attachments as $attachment)
                        <div wire:key="attachment-browser-item-{{ $attachment->id }}">
                            <x-filament-attachment-library::attachment.browser-item
                                :$attachment
                                :layout="$layout"
                                :selected="$attachment->isSelected($selected)"
                            />
                        </div>
                    @endforeach
                </x-filament-attachment-library::items.container>
            @endif

            @if($attachments->isEmpty() && $directories->isEmpty())
                <x-filament-attachment-library::empty-path-notice :$currentPath/>
            @endif
        </div>

        <x-filament-attachment-library::sidebar :$selected :$currentPath class="order-1 md:order-2"/>

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
