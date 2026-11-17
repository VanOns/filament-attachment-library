const OVERLAY_BOTTOM_GAP = 24
const OVERLAY_MIN_HEIGHT = 160

// Marks a drag of attachments inside the browser, as opposed to files dragged in from the desktop.
const ATTACHMENT_DRAG_TYPE = 'application/x-attachment-library'
const MAX_DIRECTORY_RESULTS = 50
// How long a drag has to rest on a closed directory before the tree opens it.
const SPRING_LOAD_DELAY = 700
// How long the tree waits after the last open or close before it has the server store its state.
const REMEMBER_DELAY = 400

// A PHP array can round-trip through Livewire as an object.
const asArray = (value) => (Array.isArray(value) ? value : Object.values(value ?? {}))

const findAttachmentModal = (level) => document.querySelector(`[data-attachment-modal-level="${level}"]`)

/**
 * Resolve the browser modal of the given stack level, asking the modal stack to render it first
 * when it does not exist yet.
 */
const attachmentModal = (level) => {
    const existing = findAttachmentModal(level)
    if (existing) return Promise.resolve(existing)

    return new Promise((resolve) => {
        const observer = new MutationObserver(() => {
            const modal = findAttachmentModal(level)
            if (!modal) return

            observer.disconnect()
            // Let Alpine initialise the new modal before it receives the open events.
            Alpine.nextTick(() => resolve(modal))
        })

        observer.observe(document.body, { childList: true, subtree: true })
        Livewire.dispatch('grow-attachment-modal-stack', { level })
    })
}

/**
 * Shared drop-to-upload pipeline, used by the attachment browser (uploads to its own
 * Livewire component) and the attachment field (uploads to its nested uploader component).
 *
 * Config:
 * - maxBytes: ?int        Livewire temp-upload limit; oversized files are rejected client-side
 * - mime: ?string         mime pattern ('image/*' or exact); null accepts everything
 * - extensions: string[]  lowercase extensions; empty accepts everything
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
        if (config.wireDisabled ? this.$wire.disabled : (config.disabled ?? false)) return true

        // Block drops while a modal above this zone is open (e.g. the create-directory modal
        // above the library, or a nested browser level) — Filament marks open modals with the
        // fi-modal-open class, and later modals in the document render on top.
        return Array.from(document.querySelectorAll('.fi-modal-open')).some(
            (modal) =>
                !modal.contains(this.$root) &&
                this.$root.compareDocumentPosition(modal) & Node.DOCUMENT_POSITION_FOLLOWING,
        )
    },

    // The extension is checked on the name: browsers often report no type for e.g. .srt files.
    matchesFilter(file) {
        const extensions = config.extensions ?? []
        if (extensions.length && !extensions.includes(file.name.split('.').pop().toLowerCase())) return false

        if (!config.mime) return true
        if (config.mime.endsWith('/*')) return file.type.startsWith(config.mime.slice(0, -1))

        return file.type === config.mime
    },

    uploadTarget() {
        if (!config.nestedUploader) return this.$wire

        const marker = this.$root.querySelector('[data-attachment-uploader]')

        return marker ? Livewire.find(marker.getAttribute('wire:id')) : null
    },

    notifyFile(title, body) {
        new FilamentNotification().title(title).body(body).danger().send()
    },

    onDragEnter(event) {
        if (!this.isFileDrag(event) || this.dropDisabled()) return

        this.dragDepth++
        this.$nextTick(() => this.updateOverlayHeight())
    },

    onDragLeave() {
        if (this.dragDepth > 0) this.dragDepth--
    },

    handleDrop(event) {
        this.dragDepth = 0

        // Directories arrive as 0-byte entries without a mime type; skip them.
        const files = Array.from(event.dataTransfer.files ?? []).filter((file) => file.size > 0 || file.type !== '')

        this.uploadFiles(files)
    },

    openFileDialog() {
        this.$refs.fileInput?.click()
    },

    onFilesChosen(event) {
        this.uploadFiles(Array.from(event.target.files ?? []))

        // Clear so picking the same file again still fires a change event.
        event.target.value = ''
    },

    // Shared pipeline for dropped and explorer-picked files: constraint
    // filtering, then upload into the component's droppedFiles property.
    uploadFiles(files) {
        if (this.dropDisabled() || this.uploading) return

        files
            .filter((file) => !this.matchesFilter(file))
            .forEach((file) => {
                this.notifyFile(file.name, config.messages.wrongType)
            })
        files = files.filter((file) => this.matchesFilter(file))

        // Pre-check Livewire's temp-upload size limit so oversized files fail per-file, by name.
        if (config.maxBytes) {
            files
                .filter((file) => file.size > config.maxBytes)
                .forEach((file) => {
                    this.notifyFile(file.name, config.messages.tooLarge)
                })
            files = files.filter((file) => file.size <= config.maxBytes)
        }

        // Components may impose a file count limit (e.g. the field's maxItems).
        files = this.limitFiles ? this.limitFiles(files) : files

        if (!files.length) return

        const target = this.uploadTarget()
        if (!target) return

        this.uploading = true
        this.progress = 0
        this.$nextTick(() => this.updateOverlayHeight())

        let filesFinished = 0
        const total = files.length

        const fileFinished = (index) => {
            filesFinished++

            if (filesFinished >= total) {
                this.uploading = false
                this.progress = 0
            } else {
                uploadFile(index + 1)
            }
        }

        const uploadFile = (index) => {
            if (index >= total) {
                return
            }

            const file = files[index]

            target.upload(
                'droppedFiles',
                file,
                () => {
                    fileFinished(index)
                },
                () => {
                    this.notifyFile(file.name, config.messages.failed)
                    fileFinished(index)
                },
                (event) => {
                    this.progress = Math.round(((filesFinished + event.detail.progress / 100) / total) * 100)
                },
            )
        }

        uploadFile(0)
    },

    // The sticky box is sized to the visible window: from its current top down to the viewport bottom.
    updateOverlayHeight() {
        if (!config.measureOverlay) return

        const box = this.$refs.dropBox
        if (!box || (this.dragDepth === 0 && !this.uploading)) return

        box.style.height =
            Math.max(OVERLAY_MIN_HEIGHT, window.innerHeight - box.getBoundingClientRect().top - OVERLAY_BOTTOM_GAP) +
            'px'
    },
})

/**
 * Moving attachments by dropping them on a directory: a directory card or a row of the directory
 * tree. Paths are relative to the base path, '' being the root; the server resolves and checks them.
 */
const moveTargets = () => ({
    dropPath: null,

    isAttachmentDrag(event) {
        return Array.from(event.dataTransfer?.types ?? []).includes(ATTACHMENT_DRAG_TYPE)
    },

    onMoveOver(event, path) {
        if (!this.isAttachmentDrag(event) || this.$wire.disabled) return

        event.preventDefault()
        event.dataTransfer.dropEffect = 'move'
        this.dropPath = path
    },

    onMoveLeave(event, path) {
        // Moving between the children of a target is not leaving it.
        if (event.currentTarget.contains(event.relatedTarget)) return

        if (this.dropPath === path) this.dropPath = null
    },

    onMoveDrop(event, path) {
        if (!this.isAttachmentDrag(event)) return

        event.preventDefault()
        // The browser around the target is a drop zone for uploads; this drop is not one.
        event.stopPropagation()
        this.dropPath = null

        let ids
        try {
            ids = JSON.parse(event.dataTransfer.getData(ATTACHMENT_DRAG_TYPE))
        } catch {
            return
        }

        if (!Array.isArray(ids) || !ids.length || this.$wire.disabled) return

        this.$wire.moveAttachments(ids, path === '' ? null : path)
    },
})

document.addEventListener('alpine:init', () => {
    Alpine.data('attachmentDropZone', dropZone)

    Alpine.data('attachmentBrowser', (config) => ({
        ...dropZone(config),
        ...moveTargets(),
    }))

    /**
     * Opening, closing and searching happen here without a round-trip. Every row carries its path
     * in data-directory and its controls a data-tree-action; the tree listens once for all of them.
     */
    Alpine.data('attachmentDirectoryTree', (config) => ({
        expanded: config.expanded,
        collapsed: config.collapsed,
        query: '',
        // The row whose menu is open, and where the menu goes: { path, top }.
        menu: null,
        // The directory that was just clicked, until the server has opened it.
        pending: null,
        // Only matters on a narrow screen, where the tree is a block above the files that opens on demand.
        narrowOpen: false,
        springPath: null,
        springTimer: null,
        rememberTimer: null,

        init() {
            this.reveal(this.currentPath())

            this.$watch('$wire.currentPath', () => {
                this.pending = null
                this.reveal(this.currentPath())
            })
            this.$watch('expanded', () => this.remember())
            this.$watch('collapsed', () => this.remember())
        },

        destroy() {
            this.clearSpring()
            clearTimeout(this.rememberTimer)
        },

        // The entangled state only travels with the next request; without one, a reload would lose it.
        remember() {
            clearTimeout(this.rememberTimer)
            this.rememberTimer = setTimeout(() => this.$wire.rememberTree(), REMEMBER_DELAY)
        },

        currentPath() {
            return this.$wire.currentPath ?? ''
        },

        expandedPaths() {
            return asArray(this.expanded)
        },

        isExpanded(path) {
            return path === '' || this.expandedPaths().includes(path)
        },

        // A clicked directory counts as the current one right away, so the click is never left unanswered.
        isCurrent(path) {
            return (this.pending ?? this.currentPath()) === path
        },

        expand(path) {
            if (!this.isExpanded(path)) this.expanded = [...this.expandedPaths(), path]
        },

        toggle(path) {
            if (path === '') return

            this.expanded = this.isExpanded(path)
                ? this.expandedPaths().filter((item) => item !== path)
                : [...this.expandedPaths(), path]
        },

        parentPath(path) {
            return path.split('/').slice(0, -1).join('/')
        },

        reveal(path) {
            const segments = path.split('/').filter(Boolean)

            segments.slice(0, -1).forEach((segment, index) => this.expand(segments.slice(0, index + 1).join('/')))
            this.$nextTick(() => this.scrollToCurrent())
        },

        rowOf(path) {
            return this.$refs.tree.querySelector(`.fal-tree-row[data-directory="${CSS.escape(path)}"]`)
        },

        // Moves the tree's own scroll area only: scrollIntoView() would scroll the page along.
        scrollToCurrent() {
            const scroller = this.$refs.scroller
            const row = this.rowOf(this.pending ?? this.currentPath())
            if (!row || row.offsetParent === null) return

            const top = row.getBoundingClientRect().top - scroller.getBoundingClientRect().top
            if (top >= 0 && top + row.offsetHeight <= scroller.clientHeight) return

            scroller.scrollTop += top - (scroller.clientHeight - row.offsetHeight) / 2
        },

        waitFor(path, request) {
            this.pending = path

            request
                // Livewire has already reported a failed request.
                .catch(() => {})
                .finally(() => {
                    // A later click may already be waiting for its own answer.
                    if (this.pending === path) this.pending = null
                })
        },

        open(path) {
            this.query = ''
            this.narrowOpen = false
            this.closeMenu()
            this.waitFor(path, this.$wire.openDirectory(path === '' ? null : path))
            this.reveal(path)
        },

        toggleNarrow() {
            this.narrowOpen = !this.narrowOpen
            this.closeMenu()

            if (this.narrowOpen) this.$nextTick(() => this.scrollToCurrent())
        },

        currentName() {
            const path = this.pending ?? this.currentPath()

            return path === '' ? config.rootName : path.split('/').at(-1)
        },

        setCollapsed(collapsed) {
            this.collapsed = collapsed

            // The clicked button is hidden now: hand the focus to the one that undoes it.
            this.$nextTick(() => {
                ;(collapsed ? this.$refs.expandButton : this.$refs.collapseButton)?.focus()

                if (!collapsed) this.scrollToCurrent()
            })
        },

        searching() {
            return this.query.trim() !== ''
        },

        // Read from the rows: a morph keeps those current, where a copy kept here would go stale.
        results() {
            const query = this.query.trim().toLowerCase()
            if (!query) return []

            return Array.from(this.$refs.tree.querySelectorAll('.fal-tree-row'))
                .map((row) => row.dataset.directory.split('/'))
                .filter((segments) => segments.at(-1).toLowerCase().includes(query))
                .slice(0, MAX_DIRECTORY_RESULTS)
                .map((segments) => ({
                    path: segments.join('/'),
                    name: segments.at(-1),
                    parents: segments.slice(0, -1).join(' / '),
                }))
        },

        row(event) {
            return event.target.closest?.('[data-directory]') ?? null
        },

        onTreeClick(event) {
            const control = event.target.closest('[data-tree-action]')
            const row = this.row(event)
            if (!control || !row) return

            const path = row.dataset.directory

            if (control.dataset.treeAction === 'toggle') return this.toggle(path)
            if (control.dataset.treeAction === 'menu') return this.toggleMenu(control, path)

            this.open(path)
        },

        onTreeKeydown(event) {
            if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(event.key)) return
            if (event.target.dataset?.treeAction !== 'open') return

            const row = this.row(event)
            if (!row) return

            event.preventDefault()

            const path = row.dataset.directory
            const hasChildren = 'hasChildren' in row.dataset

            if (event.key === 'ArrowRight') return hasChildren && this.expand(path)

            if (event.key === 'ArrowLeft') {
                if (hasChildren && this.isExpanded(path)) return this.toggle(path)

                return this.rowOf(this.parentPath(path))?.querySelector('[data-tree-action="open"]')?.focus()
            }

            const rows = Array.from(this.$refs.scroller.querySelectorAll('[data-tree-action="open"]')).filter(
                (button) => button.offsetParent !== null,
            )

            rows[rows.indexOf(event.target) + (event.key === 'ArrowDown' ? 1 : -1)]?.focus()
        },

        toggleMenu(control, path) {
            if (this.menu?.path === path) return this.closeMenu()

            this.menu = {
                path,
                // Relative to the panel around the list, where the menu lives.
                top: control.getBoundingClientRect().bottom - this.$refs.menu.parentElement.getBoundingClientRect().top,
            }
            this.$nextTick(() => this.$refs.menu.querySelector('button')?.focus())
        },

        closeMenu(refocus = false) {
            if (!this.menu) return

            const trigger = this.rowOf(this.menu.path)?.querySelector('[data-tree-action="menu"]')

            this.menu = null

            if (refocus) trigger?.focus()
        },

        // The click that opens the menu on another row also lands outside the open menu.
        onMenuOutside(event) {
            if (event.target.closest?.('[data-tree-action="menu"]')) return

            this.closeMenu()
        },

        onMenuFocusOut(event) {
            if (event.relatedTarget && !this.$refs.menu.contains(event.relatedTarget)) this.closeMenu()
        },

        fullPath(path) {
            return [this.$wire.basePath, path].filter(Boolean).join('/')
        },

        createSubdirectory() {
            const { path } = this.menu

            this.closeMenu()
            this.expand(path)
            this.waitFor(path, this.$wire.createSubdirectory(path))
        },

        renameDirectory() {
            const { path } = this.menu

            this.closeMenu()
            this.$wire.mountAction('renameDirectory', {
                name: path.split('/').at(-1),
                full_path: this.fullPath(path),
            })
        },

        deleteDirectory() {
            const { path } = this.menu

            this.closeMenu()
            this.$wire.mountAction('deleteDirectory', { full_path: this.fullPath(path) })
        },

        onTreeDragOver(event) {
            const row = this.row(event)
            if (!row) return

            const path = row.dataset.directory

            this.onMoveOver(event, path)

            if (this.dropPath === path) this.springLoad(row)
        },

        onTreeDragLeave(event) {
            const row = this.row(event)
            if (!row || row.contains(event.relatedTarget)) return

            if (this.dropPath === row.dataset.directory) this.dropPath = null
            this.clearSpring()
        },

        onTreeDrop(event) {
            const row = this.row(event)
            if (!row) return

            this.clearSpring()
            this.onMoveDrop(event, row.dataset.directory)
        },

        springLoad(row) {
            const path = row.dataset.directory

            if (!('hasChildren' in row.dataset) || this.isExpanded(path) || this.springPath === path) return

            this.clearSpring()
            this.springPath = path
            this.springTimer = setTimeout(() => {
                if (this.dropPath === path) this.expand(path)

                this.clearSpring()
            }, SPRING_LOAD_DELAY)
        },

        clearSpring() {
            clearTimeout(this.springTimer)
            this.springPath = null
            this.springTimer = null
        },
    }))

    /**
     * AttachmentField: the drop zone plus the field's own state handling.
     *
     * Extra config: state (entangled), statePath, multiple, selectedEvent, uploadedEvent.
     */
    Alpine.data('attachmentField', (config) => ({
        ...dropZone(config),

        state: config.state,

        init() {
            this.onSelected = (event) => {
                this.state = event.detail.selected
            }
            this.onUploaded = (event) => {
                this.mergeUploaded(event.detail.ids)
            }

            window.addEventListener(config.selectedEvent, this.onSelected)
            window.addEventListener(config.uploadedEvent, this.onUploaded)
        },

        destroy() {
            window.removeEventListener(config.selectedEvent, this.onSelected)
            window.removeEventListener(config.uploadedEvent, this.onUploaded)
        },

        // Opens the next stack level, so picking from inside a browser modal (e.g. its edit
        // action) keeps the state of the browser below.
        async openBrowser(highlight = null) {
            const level = Math.min(
                document.querySelectorAll('[data-attachment-modal-level].fi-modal-open').length,
                window.filamentData.fal.maxModalLevels - 1,
            )
            const id = (await attachmentModal(level)).dataset.fiModalId

            this.$dispatch('open-attachment-modal', {
                mime: config.mime,
                selected: this.state,
                multiple: config.multiple,
                statePath: config.statePath,
                extensions: config.extensions,
                disableMimeFilter: config.mime !== null || config.extensions.length > 0,
                highlight: highlight,
                modalId: id,
            })
            this.$dispatch('open-modal', { id })
        },

        onAttachmentRemoved(event) {
            this.state = config.multiple ? this.state.filter((id) => id !== event.detail.id) : null
        },

        onAttachmentReordered(event) {
            this.state = event.detail.ids
        },

        stateIds() {
            return Array.isArray(this.state) ? this.state : this.state ? [this.state] : []
        },

        // Cap a drop to the remaining slots: 1 for single fields, maxItems minus current selection otherwise.
        limitFiles(files) {
            const remaining = config.multiple
                ? config.maxItems
                    ? Math.max(0, config.maxItems - this.stateIds().length)
                    : files.length
                : 1

            if (files.length > remaining) {
                this.notifyFile(
                    config.messages.tooMany,
                    files
                        .slice(remaining)
                        .map((file) => file.name)
                        .join(', '),
                )
            }

            return files.slice(0, remaining)
        },

        mergeUploaded(ids) {
            if (!config.multiple) {
                this.state = ids[ids.length - 1]

                return
            }

            const merged = [...new Set([...this.stateIds(), ...ids])]

            this.state = config.maxItems ? merged.slice(0, config.maxItems) : merged
        },
    }))

    /**
     * Selection state for the attachment browser grid/list. Owns the `selected` array (entangled with
     * the Livewire component) so the selection ring updates instantly on click, with no round-trip.
     * The server stays authoritative: every click also fires $wire.highlight() to refresh the info
     * sidebar, and that commit carries the entangled array back to the server for persistence/save.
     *
     * `multiple`/`disabled` are read live from $wire on every click rather than snapshotted: a single
     * browser/modal instance is shared by every field on the page, so these flip between opens and a
     * captured value would go stale (e.g. a multiple field stuck in single-select).
     */
    Alpine.data('attachmentSelection', (config) => ({
        selected: config.state,

        // A non-sequential PHP array (e.g. keys left behind by a filter without reindexing)
        // round-trips through Livewire as a JS object instead of an array; guard against that
        // shape here too, on top of keeping the PHP side reindexed.
        selectedIds() {
            return asArray(this.selected)
        },

        // Loose comparison on purpose: ids can arrive as numbers (json_encode) or strings (attribute
        // bag / DB keys), and the entangled array mixes both. == keeps the ring in sync regardless.
        isSelected(id) {
            return this.selectedIds().some((item) => item == id)
        },

        toggle(id) {
            if (this.$wire.get('disabled')) return

            if (this.isSelected(id)) {
                this.selected = this.selectedIds().filter((item) => item != id)
                this.$wire.highlight(null)

                return
            }

            this.selected = this.$wire.get('multiple') ? [...this.selectedIds(), id] : [id]
            // Show the info panel's loading state instantly; the Livewire fetch clears it on arrival.
            this.$dispatch('attachment-info-loading', { scope: this.$wire.scope })
            this.$wire.highlight(id)
        },

        // Dragging a selected attachment takes the whole selection along; any other goes alone.
        onDragStart(event, id) {
            if (this.$wire.get('disabled')) return event.preventDefault()

            // Of a selection only what is on this page comes along: the rest is not there to be dragged.
            const onPage = Array.from(this.$root.querySelectorAll('[data-attachment]')).map(
                (item) => item.dataset.attachment,
            )
            const ids = this.isSelected(id) ? this.selectedIds().filter((item) => onPage.includes(String(item))) : [id]

            event.dataTransfer.setData(ATTACHMENT_DRAG_TYPE, JSON.stringify(ids))
            event.dataTransfer.effectAllowed = 'move'

            const ghost = this.$refs.dragGhost
            const label = this.$refs.dragGhostLabel
            if (!ghost || !label) return

            label.textContent =
                ids.length === 1
                    ? (event.currentTarget.dataset.filename ?? '')
                    : (config.dragLabel ?? '').replace(':count', ids.length)
            event.dataTransfer.setDragImage(ghost, 12, 12)
        },

        onDragEnd() {
            // A drag that ends outside every target never fires a dragleave on the last one.
            this.dropPath = null
        },
    }))

    /**
     * Drag-to-reorder for the selected items in an AttachmentField. Config: group.
     */
    Alpine.data('attachmentSortable', (config) => ({
        init() {
            if (!window.Sortable) return

            // Stop the SortableJS 'end' event from bubbling to Filament components that also sort (e.g. repeaters).
            this.$el.addEventListener('end', (event) => event.stopPropagation(), true)

            new window.Sortable(this.$el, {
                animation: 150,
                draggable: '[data-attachment-id]',
                handle: '[data-drag-handle]',
                ghostClass: 'opacity-50',
                group: config.group,
                onEnd: () => {
                    const ids = Array.from(this.$el.querySelectorAll('[data-attachment-id]')).map((el) =>
                        Number(el.dataset.attachmentId),
                    )
                    this.$dispatch('attachment-reordered', { ids })
                },
            })
        },
    }))

    /**
     * The attachment browser inside the modal is lazy-loaded, so it misses events dispatched
     * before its first load (e.g. the open-attachment-modal payload carrying the statePath when
     * the modal is first opened). Buffer the latest payload and replay it once the component
     * announces itself via attachment-browser-loaded. Config: scope (the modal id).
     */
    Alpine.data('attachmentModalBuffer', (config) => ({
        pendingOpen: null,

        init() {
            this.onOpen = (event) => {
                if ((event.detail.modalId ?? 'attachment-modal') !== config.scope) return

                this.pendingOpen = event.detail
            }
            this.onLoaded = (event) => {
                if (event.detail.scope !== config.scope || !this.pendingOpen) return

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

        reset() {
            this.state = { x: 50, y: 50 }
        },

        // Used by the crop previews, so they follow the marker.
        objectPosition() {
            return `${this.state?.x ?? 50}% ${this.state?.y ?? 50}%`
        },
    }))
})
