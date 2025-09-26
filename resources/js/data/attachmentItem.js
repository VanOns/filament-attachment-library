document.addEventListener('alpine:init', () => {
    Alpine.data('attachmentItem', ({ attachment }) => ({
        get statePath() {
            return this.$wire.statePath;
        },

        get attachments() {
            return this.$wire.attachments;
        },

        get isAttachment() {
            return attachment.class === 'attachment';
        },

        get isDirectory() {
            return attachment.class === 'directory';
        },

        get isImage() {
            return attachment.is_image;
        },

        get isVideo() {
            return attachment.is_video;
        },

        get isSelected() {
            return Alpine.store('attachmentBrowser')?.isSelected(attachment.id, this.statePath);
        },

        init() {
            this.$el.addEventListener('click', this.handleItemClick.bind(this));
            this.$el.addEventListener('contextmenu', this.handleContextMenu.bind(this));
        },

        handleItemClick() {
            Alpine.store('attachmentBrowser')?.handleItemClick(attachment, this.statePath);
        },

        handleContextMenu(event) {
            event.preventDefault();
            event.stopPropagation();

            const toggleButton = this.$el.querySelector('.toggle');
            toggleButton?.parentNode?.click();

            return false;
        },
    }));
});
