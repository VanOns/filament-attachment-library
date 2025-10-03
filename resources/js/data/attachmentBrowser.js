document.addEventListener('alpine:init', () => {
    Alpine.data('attachmentBrowserData', () => ({
        init() {
            this.$el.addEventListener('dragover', (event) => {
                event.preventDefault();

                this.$dispatch('open-section', {
                    id: 'upload-attachment-form',
                });
            });
        },

        get search() {
            return this.$wire.search;
        },
        set search(value) {
            this.$wire.search = value;
        },
    }));
});
