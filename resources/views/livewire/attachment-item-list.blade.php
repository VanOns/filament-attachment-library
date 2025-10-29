@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\Enums\Layout $layout
     */
@endphp

<div
    x-data="attachmentItemList"
    @class([
        'flex-1 flex flex-wrap gap-4 content-start w-full',
        'opacity-50 pointer-events-none' => $disabled,
        $class,
    ])
>

    {{-- Empty directory notice --}}
    <template x-if="attachments?.length === 0">
        <x-filament-attachment-library::empty-path-notice />
    </template>

    <template x-if="typeof attachments !== undefined">

        @if($layout->isList())
            <x-filament-attachment-library::attachment-item-list.list :$inModal />
        @else
            <x-filament-attachment-library::attachment-item-list.grid />
        @endif
    </template>
</div>
