<x-filament::page
    x-on:attachment-browser-loaded-js.window.once="
        $store.attachmentBrowser.setCurrentState('default', {state: null, multiple: false, showActions: true, showMime: true, statePath: 'default'})
    "
>
    <livewire:attachment-browser />

</x-filament::page>
