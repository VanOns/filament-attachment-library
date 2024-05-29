document.addEventListener('alpine:init', () => {
    Alpine.directive('clipboard', (el, {expression}, {evaluate}) => {
        el.addEventListener('click', () => {
            navigator.clipboard.writeText(evaluate(expression));
            new FilamentNotification().title('Tekst naar klembord gekopieerd').success().send()
        })
    })
})