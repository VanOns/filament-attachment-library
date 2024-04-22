Alpine.store('attachmentBrowser', {
    states: {},
    currentStatePath: null,

    init() {
        window.addEventListener('close-modal', $event => {this.close($event);})
        window.addEventListener('open-modal', $event => {this.open($event);})
        window.addEventListener('attachment-browser-add-state-path', $event => {this.addStatePath($event);})
    },

    openPath(path) {
        this.resetSelection();

        Livewire.dispatch('open-path', {path: path});
    },

    open($event) {
        if ($event.detail.id !== 'attachment-modal') return;

        this.currentStatePath = $event.detail.statePath;
    },

    close($event) {
        if ($event.detail.id !== 'attachment-modal') return;

        this.update();

        this.currentStatePath = null;
    },

    select(id, alternativeStatePath = null) {
        let statePath = alternativeStatePath ?? this.currentStatePath;

        if (! this.states[statePath].multiple) this.states[statePath].state = id;
        if (this.states[statePath].multiple) this.states[statePath].state.push(id);

        this.update();

        Livewire.dispatch('highlight-attachment', {id: id});
    },

    deselect(id, alternativeStatePath = null) {
        let statePath = alternativeStatePath ?? this.currentStatePath;

        if(!(statePath in this.states)) return false;
        if(this.states[statePath] === null) return false;
        if(this.states[statePath].multiple){
            this.states[statePath].state = this.states[statePath].state.filter(e => e !== id);
        } else {
            this.states[statePath].state = null;
        }

        this.update(statePath);

        Livewire.dispatch('highlight-attachment', {id: null});
    },

    isSelected(id, alternativeStatePath = null) {
        let statePath = alternativeStatePath ?? this.currentStatePath;

        if(!(statePath in this.states)) return false;
        if(this.states[statePath] === null) return false;
        if(this.states[statePath].multiple){
            return this.states[statePath].state.includes(id);
        }
        return this.states[statePath].state === id;
    },

    resetSelection(){
        this.states[this.currentStatePath].state = [];
    },

    update(statePath = null){
        Livewire.dispatch('selected-attachments-updated', {attachments: this.states[statePath ?? this.currentStatePath].state, statePath: statePath ?? this.currentStatePath});
    },

    showActions(statePath = null) {
        if (this.states[statePath ?? this.currentStatePath] === undefined) return false;

        return this.states[statePath ?? this.currentStatePath].showActions;
    },

    setCurrentState(statePath, state){
        this.currentStatePath = statePath;
        this.addStatePath(statePath, state);
    },

    addStatePath(statePath, state){
        this.states[statePath] = state;
    }
})
