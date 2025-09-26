document.addEventListener('alpine:init', () => {
    Alpine.data('attachmentItemList', () => ({
        get statePath() {
            return this.$wire.statePath;
        },

        get attachments() {
            return this.$wire.attachments;
        },

        init() {
            this.$el.addEventListener('contextmenu', this.handleContextMenu.bind(this));
        },

        isAttachment(attachment) {
            return attachment.class === 'attachment';
        },

        isDirectory(attachment) {
            return attachment.class === 'directory';
        },

        isImage(attachment) {
            return attachment.is_image;
        },

        isVideo(attachment) {
            return attachment.is_video;
        },

        isSelected(attachment) {
            return Alpine.store('attachmentBrowser')?.isSelected(attachment.id, this.statePath);
        },

        handleItemClick(attachment) {
            Alpine.store('attachmentBrowser')?.handleItemClick(attachment, this.statePath);
        },

        handleContextMenu(event) {
            event.preventDefault();

            const toggleButton = this.$el.querySelector('.toggle');
            toggleButton?.parentNode?.click();

            return false;
        },
    }));
});
