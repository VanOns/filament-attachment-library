document.addEventListener('alpine:init', () => {
    Alpine.directive('clipboard', (el) => {
        let text = el.textContent

        el.addEventListener('click', () => {
            navigator.clipboard.writeText(text);
            new FilamentNotification().title('Tekst naar klembord gekopieerd').success().send()
        })
    })
})