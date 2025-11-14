<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    x-data="{ state: $wire.entangle('{{ $getStatePath() }}').live }"
>
    <div
        {{-- We add events here because blade components cause issues with dynamic attribute names --}}
        x-on:attachment-removed="
            state = {{ json_encode($getMultiple()) }}
                ? state.filter(id => id !== $event.detail.id)
                : null
        "
        x-on:attachments-selected-{{ md5($getStatePath()) }}.window="console.log($event); state = $event.detail.selected"
    >
        <x-filament-attachment-library::items.field :attachments="$getAttachments()" :statePath="$getStatePath()" />

        <x-filament::button
            x-on:click="$dispatch('open-attachment-modal', {
                mime: {{ json_encode($getMime()) }},
                selected: state,
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
