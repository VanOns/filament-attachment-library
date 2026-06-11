<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        class="relative"
        {{-- The field wrapper component does not forward attributes, so the Alpine scope lives here. --}}
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}').live,
            dragDepth: 0,
            uploading: false,
            progress: 0,
            maxBytes: @js(\VanOns\FilamentAttachmentLibrary\Support\TemporaryUploadLimit::bytes()),
            mime: @js($getMime()),
            disabled: @js($isDisabled()),
            openBrowser(highlight = null) {
                this.$dispatch('open-attachment-modal', {
                    mime: this.mime,
                    selected: this.state,
                    multiple: {{ json_encode($getMultiple()) }},
                    statePath: {{ json_encode($getStatePath()) }},
                    disableMimeFilter: this.mime !== null,
                    highlight: highlight,
                });
                this.$dispatch('open-modal', { id: 'attachment-modal' });
            },
            isFileDrag(event) {
                return Array.from(event.dataTransfer?.types ?? []).includes('Files')
            },
            matchesMime(file) {
                if (! this.mime) return true
                if (this.mime.endsWith('/*')) return file.type.startsWith(this.mime.slice(0, -1))
                return file.type === this.mime
            },
            handleDrop(event) {
                this.dragDepth = 0
                if (this.disabled || this.uploading) return
                {{-- Directories arrive as 0-byte entries without a mime type; skip them --}}
                let files = Array.from(event.dataTransfer.files ?? [])
                    .filter((file) => file.size > 0 || file.type !== '')
                files.filter((file) => ! this.matchesMime(file)).forEach((file) => {
                    new FilamentNotification()
                        .title(file.name)
                        .body(@js(__('filament-attachment-library::notifications.attachment.upload_failed_wrong_type')))
                        .danger()
                        .send()
                })
                files = files.filter((file) => this.matchesMime(file))
                if (this.maxBytes) {
                    files.filter((file) => file.size > this.maxBytes).forEach((file) => {
                        new FilamentNotification()
                            .title(file.name)
                            .body(@js(__('filament-attachment-library::notifications.attachment.upload_failed_too_large', ['max' => \VanOns\FilamentAttachmentLibrary\Support\TemporaryUploadLimit::label()])))
                            .danger()
                            .send()
                    })
                    files = files.filter((file) => file.size <= this.maxBytes)
                }
                if (! files.length) return
                this.uploading = true
                this.progress = 0
                const reset = () => { this.uploading = false; this.progress = 0 }
                const fail = () => {
                    reset()
                    new FilamentNotification()
                        .title(@js(__('filament-attachment-library::notifications.attachment.upload_failed')))
                        .body(files.map((file) => file.name).join(', '))
                        .danger()
                        .send()
                }
                const uploader = Livewire.find(this.$root.querySelector('[data-attachment-uploader]')?.closest('[wire\\:id]')?.getAttribute('wire:id'))
                if (! uploader) return reset()
                uploader.uploadMultiple('droppedFiles', files, reset, fail, (e) => { this.progress = e.detail.progress })
            },
        }"
        x-on:dragenter.prevent="if (isFileDrag($event) && ! disabled) dragDepth++"
        x-on:dragover.prevent
        x-on:dragleave.prevent="if (dragDepth > 0) dragDepth--"
        x-on:drop.prevent="handleDrop($event)"
        {{-- We add events here because blade components cause issues with dynamic attribute names --}}
        x-on:attachment-removed="
            state = {{ json_encode($getMultiple()) }}
                ? state.filter(id => id !== $event.detail.id)
                : null
        "
        x-on:attachment-reordered="state = $event.detail.ids"
        x-on:attachments-selected-{{ md5($getStatePath()) }}.window="state = $event.detail.selected"
        x-on:attachments-uploaded-{{ md5($getStatePath()) }}.window="
            state = {{ json_encode($getMultiple()) }}
                ? [...new Set([...(Array.isArray(state) ? state : (state ? [state] : [])), ...$event.detail.ids])]
                : $event.detail.ids[$event.detail.ids.length - 1]
        "
    >
        <livewire:attachment-field-uploader
            :statePath="$getStatePath()"
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
