<x-dynamic-component
    x-data="{
        state: $wire.entangle('{{ $getStatePath() }}').live,
    }"
    :component="$getFieldWrapperView()"
    :field="$field"
    x-on:attachments-selected="state = $event.detail.selected"
    x-on:attachment-removed="state = {{ json_encode($getMultiple()) }} ? state.filter(id => id !== $event.detail.id) : null"
>
    <div>
        <x-filament-attachment-library::field-items :attachments="$getAttachments()" :statePath="$getStatePath()" />

        <x-filament::button
            x-on:click="$dispatch('open-modal', { id: 'attachment-modal-{{ $getStatePath() }}' })"
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
    @php
        /**
         * TODO: $multiple, $mime, $disabled
         */
    @endphp
    <x-filament-attachment-library::attachment-browser-modal :multiple="$getMultiple()" :statePath="$getStatePath()" />
</x-dynamic-component>
