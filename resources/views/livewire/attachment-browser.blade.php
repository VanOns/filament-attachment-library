<div
    {{-- Dispatch attachment browser loaded event --}}
    data-dispatch="attachment-browser-loaded"
    {{-- Load attachment browser javascript --}}
    x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('attachmentBrowser'))]"
    x-data="{search: $wire.entangle('search').live}"
    x-on:dragover.prevent="$dispatch('open-section', {id: 'upload-attachment-form'})"
>

    <div class="flex justify-between align-center mb-6 items-center flex-wrap">
        {{-- Breadcrumbs --}}
        <x-filament-attachment-library::breadcrumbs />

        {{-- Filtering, sorting and header actions --}}
        <x-filament-attachment-library::header-actions :$layout />
        <x-filament-attachment-library::header-actions-mobile :$layout />
    </div>

    {{-- Search result indicator --}}
    <div x-show="search">
        <h1>{{ __('filament-attachment-library::views.browser.search_results') }} <span x-text="search"></span></h1>
    </div>

    {{-- Main attachment browser content --}}
    <div class="flex flex-col gap-6 mt-4 flex-wrap md:flex-row">
        {{-- Attachment list --}}
        <livewire:attachment-item-list
            :attachments="$this->paginator->getCollection()"
            :$currentPath
            :$layout
            :$inModal
            class="order-2 md:order-1"
        />

        {{-- Include sidebar cards --}}
        <x-filament-attachment-library::sidebar class="order-1 md:order-2" />

        {{-- Pagination --}}
        <div class="mt-4 w-full order-3">
            <x-filament::pagination :paginator="$this->paginator" extreme-links />
        </div>
    </div>

    <x-filament-actions::modals/>

</div>
