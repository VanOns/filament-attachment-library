document.addEventListener('alpine:init', () => {
    Alpine.data('emptyPathNotice', () => ({
        get currentPath() {
            return this.$wire.currentPath;
        },

        get showButton() {
            return this.currentPath !== null && this.currentPath !== '';
        },
    }));
});
