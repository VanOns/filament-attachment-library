<x-dynamic-component
    x-data="attachmentBrowserField({
        statePath: '{{ $getStatePath() }}',
        multiple: {{ $getMultiple() ? 'true' : 'false' }},
        showActions: true,
        showMime: false,
        mime: '{{ $getMime() }}',
        disabled: {{ $isDisabled() ? 'true' : 'false' }}
    })"
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div>
        <x-filament-attachment-library::field-items :attachments="$getAttachments()" :statePath="$getStatePath()" />

        <x-filament::button
            x-on:click="$dispatch('open-modal', { id: 'attachment-modal', statePath: '{{ $getStatePath() }}' })"
            x-on:selected-attachments-updated.window="$event.detail.statePath === '{{ $getStatePath() }}' ? $wire.$set('{{ $getStatePath() }}', $event.detail.attachments) : ''"
            icon="heroicon-o-document"
            :disabled="$isDisabled()"
            @class([
                'mt-2',
                'opacity-50 pointer-events-none' => $isDisabled(),
            ])
        >
            {{ __('filament-attachment-library::views.field.pick') }}
        </x-filament::button>

    </div>

</x-dynamic-component>
