document.addEventListener('alpine:init', () => {
    Alpine.data('attachmentActions', ({ attachment }) => ({
        get statePath() {
            return this.$wire.statePath;
        },

        get isAttachment() {
            return attachment.class === 'attachment';
        },

        get isDirectory() {
            return attachment.class === 'directory';
        },

        get isSelected() {
            return Alpine.store('attachmentBrowser')?.isSelected(attachment.id, this.statePath);
        },

        get showDropdown() {
            return Alpine.store('attachmentBrowser')?.showActions(this.statePath);
        },

        get showIcon() {
            return (this.isAttachment && !this.isSelected) || this.isDirectory;
        },

        get showSelectIcons() {
            return this.isAttachment && this.isSelected;
        },

        viewDetails() {
            this.$dispatch('show-attachment-info', { attachment });
        },

        openFile() {
            window.open(attachment.url);
        },

        modifyFile() {
            this.$dispatch('mount-action', {
                name: 'editAttachmentAction',
                arguments: { 'attachment_id': attachment.id },
            });
        },

        removeFile() {
            this.$dispatch('mount-action', {
                name: 'deleteAttachment',
                arguments: { 'attachment_id': attachment.id },
            });
        },

        renameDirectory() {
            this.$dispatch('mount-action', {
                name: 'renameDirectory',
                arguments: { 'directory': attachment },
            });
        },

        removeDirectory() {
            this.$dispatch('mount-action', {
                name: 'deleteDirectory',
                arguments: { 'directory': attachment },
            });
        },
    }));
});
