document.addEventListener('alpine:init', () => {
    Alpine.data('breadcrumbs', () => ({
        get statePath() {
            return this.$wire.statePath;
        },

        openSection() {
            this.$dispatch('open-section', { id: 'create-directory-form' });
        },
    }));
});
