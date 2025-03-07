Alpine.store('attachmentBrowser', {
    /**
     * Properties.
     */
    states: {},
    originalState: null,
    currentStatePath: null,

    /**
     * Constructor.
     */
    init() {
        window.addEventListener('open-modal', $event => {
            this._onModalOpened($event);
        });
        window.addEventListener('close-modal', $event => {
            this._onModalClosed($event);
        });
        window.addEventListener('attachment-browser-add-state-path', $event => {
            this.addStatePath($event);
        });
        Livewire.on('select-attachment', ([id, statePath]) => {
            this.select(id, statePath)
        })
    },

    /**
     * Getters and setters.
     */
    setCurrentState(statePath, state) {
        this.currentStatePath = statePath;
        this.addStatePath(statePath, state);
    },

    addStatePath(statePath, state) {
        this.states[statePath] = state;
    },

    isSelected(id, alternativeStatePath = null) {
        let statePath = alternativeStatePath ?? this.currentStatePath;

        if (this._statePathAbsentOrNull(statePath)) return false;

        return this._isMultiple(statePath)
            ? this.states[statePath].state.includes(id)
            : this.states[statePath].state === id;
    },

    showActions(alternativeStatePath = null) {
        const statePath = alternativeStatePath ?? this.currentStatePath;

        if (this._statePathAbsentOrNull(statePath)) return false;

        return this.states[statePath].showActions;
    },

    showMime(alternativeStatePath = null) {
        const statePath = alternativeStatePath ?? this.currentStatePath;

        if (this._statePathAbsentOrNull(statePath)) return false;

        return this.states[statePath].showMime;
    },

    /**
     * Helper methods and callbacks.
     */
    _onModalOpened($event) {
        if ($event.detail.id !== 'attachment-modal') return;

        this.currentStatePath = $event.detail.statePath;
        this.originalState = this.states[$event.detail.statePath].state;

        Livewire.dispatch('set-mime', {
            mime: this.states[this.currentStatePath]['mime'] ?? ''
        });
    },

    _onModalClosed($event) {
        if ($event.detail.id !== 'attachment-modal') return;

        if ($event.detail.save){
            this._dispatchUpdatedAttachments();
            this.currentStatePath = null;
            return;
        }

        if (!(this.originalState === this.states[this.currentStatePath].state)) {
            this.states[this.currentStatePath].state = this.originalState;
        }

        this.currentStatePath = null;
    },

    _statePathAbsentOrNull(statePath) {
        return !(statePath in this.states) || this.states[statePath] === null;
    },

    _isMultiple(statePath) {
        return this.states[statePath].multiple;
    },

    _dispatchUpdatedAttachments(alternativeStatePath = null) {
        const statePath = alternativeStatePath ?? this.currentStatePath;

        Livewire.dispatch(
            'selected-attachments-updated',
            {
                attachments: this.states[statePath].state,
                statePath: statePath
            }
        );
    },

    handleItemClick(item, alternativeStatePath = null){
        const statePath = alternativeStatePath ?? this.currentStatePath;

        switch (item.class) {
            case 'attachment':
                this.isSelected(item.id, statePath)
                    ? this.deselect(item.id, statePath)
                    : this.select(item.id, statePath);

                if (alternativeStatePath !== null) {
                    this._dispatchUpdatedAttachments(statePath);
                }

                break;
            case 'directory':
                this.openPath(item.fullPath, statePath);
                break;
        }
    },

    /**
     * Attachment and directory actions
     */
    openPath(path) {
        this.states[this.currentStatePath].state = [];

        Livewire.dispatch('open-path', {path: path});
    },

    select(id, alternativeStatePath = null) {
        let statePath = alternativeStatePath ?? this.currentStatePath;

        this._isMultiple(statePath)
            ? this.states[statePath].state.push(id)
            : this.states[statePath].state = id;

        Livewire.dispatch('highlight-attachment', {id: id});
    },

    deselect(id, alternativeStatePath = null) {
        let statePath = alternativeStatePath ?? this.currentStatePath;

        if (this._statePathAbsentOrNull(statePath)) return false;

        this.states[statePath].state = this._isMultiple(statePath)
            ? this.states[statePath].state.filter(e => e !== id)
            : null;

        Livewire.dispatch('highlight-attachment', {id: null});
    }
})
