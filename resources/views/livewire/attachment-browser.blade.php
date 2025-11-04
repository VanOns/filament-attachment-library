@php
    use VanOns\FilamentAttachmentLibrary\Enums\Layout
@endphp

<div>
    <div class="flex justify-between align-center mb-6 items-center flex-wrap">
        <x-filament-attachment-library::breadcrumbs/>
        <x-filament-attachment-library::header-actions :$layout/>
        <x-filament-attachment-library::header-actions-mobile :$layout/>
    </div>

    @if($search)
        <h1>{{ __('filament-attachment-library::views.browser.search_results') }} <span>{{ $search }}</span></h1>
    @endif

    <div class="flex flex-col gap-6 mt-4 flex-wrap md:flex-row">
        <div
            @class([
                'flex-1 order-2 md:order-1',
                'opacity-50 pointer-events-none' => $disabled,
            ])
        >
            @if($attachments->isEmpty())
                <x-filament-attachment-library::empty-path-notice :$currentPath/>
            @else
                @if($layout === Layout::LIST)
                    <x-filament-attachment-library::items.list :$inModal :$selected :$attachments/>
                @endif

                @if($layout === Layout::GRID)
                    <x-filament-attachment-library::items.grid :$selected :$attachments/>
                @endif
            @endif
        </div>

        <x-filament-attachment-library::sidebar :$currentPath class="order-1 md:order-2"/>

        <div class="mt-4 w-full order-3">
            <x-filament::pagination :paginator="$this->paginator" extreme-links/>
        </div>
    </div>

    <x-filament-actions::modals/>
</div>
