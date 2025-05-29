<x-filament::page
    x-on:attachment-browser-loaded-js.window.once="$store.attachmentBrowser.setCurrentState('default', {
        state: null,
        multiple: false,
        showActions: true,
        showMime: true,
        statePath: 'default',
        disabled: false
    })"
>

    <livewire:attachment-browser />

</x-filament::page>
