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
        @include('filament-attachment-library::components.breadcrumbs')

        {{-- Filtering, sorting and header actions --}}
        @include('filament-attachment-library::components.header-actions')
    </div>

    {{-- Search result indicator --}}
    <div x-show="search">
        <h1>{{ __('filament-attachment-library::views.browser.search_results') }} <span x-text="search"></span></h1>
    </div>

    {{-- Main attachment browser content --}}
    <div class="flex flex-row gap-6 mt-4 flex-wrap">

        {{-- Attachment list --}}
        <livewire:attachment-item-list :attachments="$this->paginator->getCollection()" :$currentPath :$layout :$inModal />

        {{-- Include sidebar cards --}}
        @include('filament-attachment-library::components.sidebar')

        {{-- Pagination --}}
        <div class="mt-4 w-full">
            <x-filament::pagination :paginator="$this->paginator" extreme-links />
        </div>
    </div>

    <x-filament-actions::modals/>

</div>
