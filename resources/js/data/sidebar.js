document.addEventListener('alpine:init', () => {
    Alpine.data('sidebar', () => ({
        showMimeOptions: false,

        init() {
            window.addEventListener('attachment-browser-loaded-js', () => {
                this.showMimeOptions = Alpine.store('attachmentBrowser')?.showMime();
            });
        },
    }));
});
