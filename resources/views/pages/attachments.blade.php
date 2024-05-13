<x-filament::page
    x-on:attachment-browser-loaded-js.window.once="
    $store.attachmentBrowser.setCurrentState('default', {state: null, multiple: false, showActions: true});
    ">
    <livewire:attachment-browser />

</x-filament::page>
