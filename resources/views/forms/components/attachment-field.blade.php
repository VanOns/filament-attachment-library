<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        {{-- The field wrapper component does not forward attributes, so the Alpine scope lives here. --}}
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}').live,
            openBrowser(highlight = null) {
                this.$dispatch('open-attachment-modal', {
                    mime: {{ json_encode($getMime()) }},
                    selected: this.state,
                    multiple: {{ json_encode($getMultiple()) }},
                    statePath: {{ json_encode($getStatePath()) }},
                    disableMimeFilter: {{ json_encode($getMime() !== null) }},
                    highlight: highlight,
                });
                this.$dispatch('open-modal', { id: 'attachment-modal' });
            },
        }"
        {{-- We add events here because blade components cause issues with dynamic attribute names --}}
        x-on:attachment-removed="
            state = {{ json_encode($getMultiple()) }}
                ? state.filter(id => id !== $event.detail.id)
                : null
        "
        x-on:attachment-reordered="state = $event.detail.ids"
        x-on:attachments-selected-{{ md5($getStatePath()) }}.window="state = $event.detail.selected"
    >
        <x-filament-attachment-library::items.field
            :attachments="$getAttachments()"
            :statePath="$getStatePath()"
            :reorderable="$getReorderable()"
            :compact="$getCompact()"
            :disabled="$isDisabled()"
        />

        <x-filament::button
            x-on:click="openBrowser()"
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
