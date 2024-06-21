<x-dynamic-component
        x-data="{ state: $wire.$entangle('{{ $getStatePath() }}').live }"
        x-on:selected-attachments-updated.window="$event.detail.statePath === '{{$getStatePath()}}' ? $wire.$set('{{$getStatePath()}}', $event.detail.attachments) : ''"
        x-on:attachment-browser-loaded-js.window="$store.attachmentBrowser.addStatePath('{{$getStatePath()}}', {state: state ?? [], multiple: '{{$getMultiple()}}', showActions: false, showMime: false, mime: '{{$getMime()}}'})"
        :component="$getFieldWrapperView()"
        :field="$field">
    <div>

        <div class="flex gap-2 flex-wrap">
            <livewire:attachment-item-list :lazy="false" :attachments="$getAttachments()" :statePath="$getStatePath()" wire:key="attachment-item-list-{{ Str::random(10) }}" />
        </div>

        <x-filament::button
                x-on:click="$dispatch('open-modal', {id: 'attachment-modal', statePath: '{{ $getStatePath() }}'})"
                x-on:selected-attachments-updated.window="$event.detail.statePath === '{{$getStatePath()}}' ? $wire.$set('{{$getStatePath()}}', $event.detail.attachments) : ''"
                class="mt-2"
                icon="heroicon-o-document">
            Kies bestand(en)
        </x-filament::button>

    </div>

</x-dynamic-component>
