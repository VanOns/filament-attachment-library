document.addEventListener('alpine:init', () => {
    Alpine.data('attachmentBrowserField', ({ statePath, multiple, showActions, showMime, mime, disabled }) => ({
        state: null,

        config: {
            state: this.state ?? [],
            multiple,
            showActions,
            showMime,
            mime,
            disabled
        },

        init() {
            this.state = this.$wire.$entangle(statePath).live;

            if (this.$store.attachmentBrowser !== undefined) {
                this.initAttachmentBrowser();
            }

            this.$watch('state', value => {
                this.$wire.$set(statePath, value);
            });

            window.addEventListener('selected-attachments-updated', this.onSelectedAttachmentsUpdated.bind(this));
            window.addEventListener('attachment-browser-loaded-js', this.initAttachmentBrowser.bind(this));
        },

        destroy() {
            window.removeEventListener('selected-attachments-updated', this.onSelectedAttachmentsUpdated.bind(this));
            window.removeEventListener('attachment-browser-loaded-js', this.initAttachmentBrowser.bind(this));
        },

        initAttachmentBrowser() {
            this.$store.attachmentBrowser.addStatePath(statePath, {
                ...this.config,
                state: this.$wire.get(statePath)
            });
        },

        onSelectedAttachmentsUpdated(event) {
            if (event.detail.statePath === statePath) {
                this.$wire.$set(statePath, event.detail.attachments);
            }
        }
    }));
});
