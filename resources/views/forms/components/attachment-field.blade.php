<x-dynamic-component
        x-data="{ state: $wire.$entangle('{{ $getStatePath() }}').live }"
        x-on:selected-attachments-updated.window="$event.detail.statePath === '{{$getStatePath()}}' ? $wire.$set('{{$getStatePath()}}', $event.detail.attachments) : ''"
        x-init="if($store.attachmentBrowser !== undefined){ $store.attachmentBrowser.addStatePath('{{$getStatePath()}}', {state: state ?? [], multiple: '{{$getMultiple()}}', showActions: true, showMime: false, mime: '{{$getMime()}}'}) }"

        {{-- Failover if attachmentBrowser isn't yet defined. --}}
        x-on:attachment-browser-loaded-js.window="$store.attachmentBrowser.addStatePath('{{$getStatePath()}}', {state: state ?? [], multiple: '{{$getMultiple()}}', showActions: true, showMime: false, mime: '{{$getMime()}}'})"
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
            {{ __('filament-attachment-library::views.field.pick') }}
        </x-filament::button>

    </div>

</x-dynamic-component>
