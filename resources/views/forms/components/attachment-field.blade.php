<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        class="relative"
        {{-- The field wrapper component does not forward attributes, so the Alpine scope lives here. --}}
        x-data="attachmentField({
            state: $wire.entangle('{{ $getStatePath() }}').live,
            statePath: @js($getStatePath()),
            multiple: @js($getMultiple()),
            mime: @js($getMime()),
            disabled: @js($isDisabled()),
            nestedUploader: true,
            maxBytes: @js(\VanOns\FilamentAttachmentLibrary\Support\TemporaryUploadLimit::bytes()),
            maxItems: @js($getMaxItems()),
            selectedEvent: @js('attachments-selected-' . md5($getStatePath())),
            uploadedEvent: @js('attachments-uploaded-' . md5($getStatePath())),
            messages: @js([
                'tooLarge' => __('filament-attachment-library::notifications.attachment.upload_failed_too_large', ['max' => \VanOns\FilamentAttachmentLibrary\Support\TemporaryUploadLimit::label()]),
                'wrongType' => __('filament-attachment-library::notifications.attachment.upload_failed_wrong_type'),
                'tooMany' => __('filament-attachment-library::notifications.attachment.upload_failed_too_many', ['max' => $getMultiple() ? ($getMaxItems() ?? '∞') : 1]),
                'failed' => __('filament-attachment-library::notifications.attachment.upload_failed'),
            ]),
        })"
        x-on:dragenter.prevent="onDragEnter($event)"
        x-on:dragover.prevent
        x-on:dragleave.prevent="onDragLeave()"
        x-on:drop.prevent="handleDrop($event)"
        x-on:attachment-removed="onAttachmentRemoved($event)"
        x-on:attachment-reordered="onAttachmentReordered($event)"
    >
        <livewire:attachment-field-uploader
            :statePath="$getStatePath()"
            :mime="$getMime()"
            :wire:key="'attachment-uploader-' . $getStatePath()"
        />

        <x-filament-attachment-library::items.field
            :attachments="$getAttachments()"
            :statePath="$getStatePath()"
            :reorderable="$getReorderable()"
            :compact="$getCompact()"
            :disabled="$isDisabled()"
        />

        <x-filament::button
            x-on:click="openBrowser()"
            icon="heroicon-o-document"
            :disabled="$isDisabled()"
            @class([
                'mt-2',
                'opacity-50 pointer-events-none' => $isDisabled(),
            ])
        >
            {{ __('filament-attachment-library::views.field.pick') }}
        </x-filament::button>

        {{-- Drop overlay --}}
        <div
            x-cloak
            x-show="dragDepth > 0 || uploading"
            class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-dashed border-primary-500 bg-white/85 backdrop-blur-sm dark:bg-gray-900/85"
        >
            <div class="rounded-full bg-primary-100 p-2 dark:bg-primary-500/20">
                <x-filament::icon icon="heroicon-o-arrow-up-tray" class="h-5 w-5 text-primary-600 dark:text-primary-400"/>
            </div>

            <span x-show="! uploading" class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ __('filament-attachment-library::views.browser.drop.prompt') }}
            </span>

            <span x-show="uploading" class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ __('filament-attachment-library::views.browser.drop.uploading') }} <span x-text="progress + '%'"></span>
            </span>
        </div>
    </div>
</x-dynamic-component>
