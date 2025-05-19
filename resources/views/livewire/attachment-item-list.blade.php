<div
    x-data="{
        attachments: $wire.$entangle('attachments'),
        statePath: $wire.statePath,
        layout: $wire.$entangle('layout'),
    }"
    @class([
        'flex-1 flex flex-wrap gap-4 content-start w-full',
        'opacity-50 pointer-events-none' => $disabled,
    ])
>

    {{-- Empty directory notice --}}
    <template x-if="attachments?.length === 0">
        <x-filament-attachment-library::empty-path-notice />
    </template>

    <template x-if="typeof attachments !== undefined">

        @switch($layout)
            @case('list')
                <x-filament-attachment-library::attachment-item-list.list :$inModal />
                @break
            @default
                <x-filament-attachment-library::attachment-item-list.grid />
                @break
        @endswitch

    </template>
</div>
