document.addEventListener('alpine:init', () => {
    Alpine.data('attachmentItemList', () => ({
        get attachments() {
            return this.$wire.attachments;
        },
        get statePath() {
            return this.$wire.statePath;
        },
    }));
});
