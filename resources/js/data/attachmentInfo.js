document.addEventListener('alpine:init', () => {
    Alpine.data('attachmentInfo', () => ({
        get attachment() {
            return this.$wire.attachment;
        },
        set attachment(value) {
            this.$wire.attachment = value;
        },
    }));
});
