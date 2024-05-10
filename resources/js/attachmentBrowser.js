Alpine.store('attachmentBrowser', {
    /**
     * Properties.
     */
    states: {},
    currentStatePath: null,

    /**
     * Constructor.
     */
    init() {
        window.addEventListener('open-modal', $event => {
            this._onModalOpened($event);
        })
        window.addEventListener('close-modal', $event => {
            this._onModalClosed($event);
        })
        window.addEventListener('attachment-browser-add-state-path', $event => {
            this.addStatePath($event);
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

    /**
     * Helper methods and callbacks.
     */
    _onModalOpened($event) {
        if ($event.detail.id !== 'attachment-modal') return;

        this.currentStatePath = $event.detail.statePath;
    },

    _onModalClosed($event) {
        if ($event.detail.id !== 'attachment-modal') return;

        this._dispatchUpdatedAttachments();

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

        switch (item.type) {
            case 'attachment':
                this.isSelected(item.id, statePath)
                    ? this.deselect(item.id, statePath)
                    : this.select(item.id, statePath);
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

        this._dispatchUpdatedAttachments();

        Livewire.dispatch('highlight-attachment', {id: id});
    },

    deselect(id, alternativeStatePath = null) {
        let statePath = alternativeStatePath ?? this.currentStatePath;

        if (this._statePathAbsentOrNull(statePath)) return false;

        this.states[statePath].state = this._isMultiple(statePath)
            ? this.states[statePath].state.filter(e => e !== id)
            : null;

        this._dispatchUpdatedAttachments(statePath);

        Livewire.dispatch('highlight-attachment', {id: null});
    }
})
