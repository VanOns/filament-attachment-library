<x-dynamic-component
    x-data="{
        state: $wire.entangle('{{ $getStatePath() }}').live,
    }"
    :component="$getFieldWrapperView()"
    :field="$field"
    x-on:attachments-selected.window="$event.detail.statePath === '{{ $getStatePath() }}' && (state = $event.detail.selected)"
    x-on:attachment-removed="state = {{ json_encode($getMultiple()) }} ? state.filter(id => id !== $event.detail.id) : null"
>
    <div>
        <x-filament-attachment-library::items.field :attachments="$getAttachments()" :statePath="$getStatePath()" />

        <x-filament::button
            x-on:click="$dispatch('open-attachment-modal', {
                mime: {{ json_encode($getMime()) }},
                multiple: {{ json_encode($getMultiple()) }},
                statePath: {{ json_encode($getStatePath()) }},
                disableMimeFilter: {{ json_encode($getMime() !== null) }},
            }); $dispatch('open-modal', { id: 'attachment-modal' })"
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
