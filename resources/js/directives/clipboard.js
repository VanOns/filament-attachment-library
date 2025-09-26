document.addEventListener('alpine:init', () => {
    Alpine.directive('clipboard', (el, {expression}, {evaluate}) => {
        el.addEventListener('click', () => {
            navigator.clipboard.writeText(evaluate(expression));
            new FilamentNotification().title(window.fal.__('labels.clipboardSuccess')).success().send();
        });
    });
});
