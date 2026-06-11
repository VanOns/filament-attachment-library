const OVERLAY_BOTTOM_GAP = 24
const OVERLAY_MIN_HEIGHT = 160

/**
 * Shared drop-to-upload pipeline, used by the attachment browser (uploads to its own
 * Livewire component) and the attachment field (uploads to its nested uploader component).
 *
 * Config:
 * - maxBytes: ?int        Livewire temp-upload limit; oversized files are rejected client-side
 * - mime: ?string         mime pattern ('image/*' or exact); null accepts everything
 * - disabled: bool        static disabled flag (field)
 * - wireDisabled: bool    read `disabled` from the Livewire component instead (browser)
 * - nestedUploader: bool  upload to the nested attachment-field-uploader instead of $wire
 * - measureOverlay: bool  size the sticky overlay box to the visible window (browser)
 * - messages: { tooLarge, wrongType, failed }
 */
const dropZone = (config) => ({
    dragDepth: 0,
    uploading: false,
    progress: 0,

    isFileDrag(event) {
        return Array.from(event.dataTransfer?.types ?? []).includes('Files')
    },

    dropDisabled() {
        return config.wireDisabled ? this.$wire.disabled : (config.disabled ?? false)
    },

    matchesMime(file) {
        if (! config.mime) return true
        if (config.mime.endsWith('/*')) return file.type.startsWith(config.mime.slice(0, -1))

        return file.type === config.mime
    },

    uploadTarget() {
        if (! config.nestedUploader) return this.$wire

        const marker = this.$root.querySelector('[data-attachment-uploader]')

        return marker ? Livewire.find(marker.getAttribute('wire:id')) : null
    },

    notifyFile(title, body) {
        new FilamentNotification().title(title).body(body).danger().send()
    },

    onDragEnter(event) {
        if (! this.isFileDrag(event) || this.dropDisabled()) return

        this.dragDepth++
        this.$nextTick(() => this.updateOverlayHeight())
    },

    onDragLeave() {
        if (this.dragDepth > 0) this.dragDepth--
    },

    handleDrop(event) {
        this.dragDepth = 0

        if (this.dropDisabled() || this.uploading) return

        // Directories arrive as 0-byte entries without a mime type; skip them.
        let files = Array.from(event.dataTransfer.files ?? [])
            .filter((file) => file.size > 0 || file.type !== '')

        files.filter((file) => ! this.matchesMime(file)).forEach((file) => {
            this.notifyFile(file.name, config.messages.wrongType)
        })
        files = files.filter((file) => this.matchesMime(file))

        // Pre-check Livewire's temp-upload size limit so oversized files fail per-file, by name.
        if (config.maxBytes) {
            files.filter((file) => file.size > config.maxBytes).forEach((file) => {
                this.notifyFile(file.name, config.messages.tooLarge)
            })
            files = files.filter((file) => file.size <= config.maxBytes)
        }

        if (! files.length) return

        const target = this.uploadTarget()
        if (! target) return

        this.uploading = true
        this.progress = 0
        this.$nextTick(() => this.updateOverlayHeight())

        const reset = () => {
            this.uploading = false
            this.progress = 0
        }

        // The whole batch fails together and the callback has no payload; name the batch's files.
        const fail = () => {
            reset()
            this.notifyFile(config.messages.failed, files.map((file) => file.name).join(', '))
        }

        target.uploadMultiple('droppedFiles', files, reset, fail, (event) => {
            this.progress = event.detail.progress
        })
    },

    // The sticky box is sized to the visible window: from its current top down to the viewport bottom.
    updateOverlayHeight() {
        if (! config.measureOverlay) return

        const box = this.$refs.dropBox
        if (! box || (this.dragDepth === 0 && ! this.uploading)) return

        box.style.height = Math.max(
            OVERLAY_MIN_HEIGHT,
            window.innerHeight - box.getBoundingClientRect().top - OVERLAY_BOTTOM_GAP,
        ) + 'px'
    },
})

document.addEventListener('alpine:init', () => {
    Alpine.data('attachmentDropZone', dropZone)

    /**
     * AttachmentField: the drop zone plus the field's own state handling.
     *
     * Extra config: state (entangled), statePath, multiple, selectedEvent, uploadedEvent.
     */
    Alpine.data('attachmentField', (config) => ({
        ...dropZone(config),

        state: config.state,

        init() {
            this.onSelected = (event) => { this.state = event.detail.selected }
            this.onUploaded = (event) => { this.mergeUploaded(event.detail.ids) }

            window.addEventListener(config.selectedEvent, this.onSelected)
            window.addEventListener(config.uploadedEvent, this.onUploaded)
        },

        destroy() {
            window.removeEventListener(config.selectedEvent, this.onSelected)
            window.removeEventListener(config.uploadedEvent, this.onUploaded)
        },

        openBrowser(highlight = null) {
            this.$dispatch('open-attachment-modal', {
                mime: config.mime,
                selected: this.state,
                multiple: config.multiple,
                statePath: config.statePath,
                disableMimeFilter: config.mime !== null,
                highlight: highlight,
            })
            this.$dispatch('open-modal', { id: 'attachment-modal' })
        },

        onAttachmentRemoved(event) {
            this.state = config.multiple
                ? this.state.filter((id) => id !== event.detail.id)
                : null
        },

        onAttachmentReordered(event) {
            this.state = event.detail.ids
        },

        mergeUploaded(ids) {
            this.state = config.multiple
                ? [...new Set([...(Array.isArray(this.state) ? this.state : (this.state ? [this.state] : [])), ...ids])]
                : ids[ids.length - 1]
        },
    }))

    /**
     * Drag-to-reorder for the selected items in an AttachmentField. Config: group.
     */
    Alpine.data('attachmentSortable', (config) => ({
        init() {
            if (! window.Sortable) return

            // Stop the SortableJS 'end' event from bubbling to Filament components that also sort (e.g. repeaters).
            this.$el.addEventListener('end', (event) => event.stopPropagation(), true)

            new window.Sortable(this.$el, {
                animation: 150,
                draggable: '[data-attachment-id]',
                handle: '[data-drag-handle]',
                ghostClass: 'opacity-50',
                group: config.group,
                onEnd: () => {
                    const ids = Array.from(this.$el.querySelectorAll('[data-attachment-id]'))
                        .map((el) => Number(el.dataset.attachmentId))
                    this.$dispatch('attachment-reordered', { ids })
                },
            })
        },
    }))

    /**
     * The attachment browser inside the modal is lazy-loaded, so it misses events dispatched
     * before its first load (e.g. the open-attachment-modal payload carrying the statePath when
     * the modal is first opened). Buffer the latest payload and replay it once the component
     * announces itself via attachment-browser-loaded.
     */
    Alpine.data('attachmentModalBuffer', () => ({
        pendingOpen: null,

        init() {
            this.onOpen = (event) => { this.pendingOpen = event.detail }
            this.onLoaded = () => {
                if (! this.pendingOpen) return

                this.$dispatch('open-attachment-modal', this.pendingOpen)
                this.pendingOpen = null
            }

            window.addEventListener('open-attachment-modal', this.onOpen)
            window.addEventListener('attachment-browser-loaded', this.onLoaded)
        },

        destroy() {
            window.removeEventListener('open-attachment-modal', this.onOpen)
            window.removeEventListener('attachment-browser-loaded', this.onLoaded)
        },
    }))

    /**
     * Focal point picker. Config: state (entangled { x, y } in percentages).
     */
    Alpine.data('attachmentFocalPicker', (config) => ({
        state: config.state,

        setPosition(event) {
            this.state = {
                x: Math.round((event.offsetX / event.target.width) * 100),
                y: Math.round((event.offsetY / event.target.height) * 100),
            }
        },
    }))
})
