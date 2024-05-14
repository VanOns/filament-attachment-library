<div {{-- Dispatch attachment browser loaded event --}}
     data-dispatch="attachment-browser-loaded"
     {{-- Load attachment browser javascript --}}
     x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('attachmentBrowser'))]"
     x-on:dragover.prevent="$dispatch('mount-action', {name: 'uploadAttachment', arguments: {}});">

    <div class="flex justify-between align-center mb-4 items-center flex-wrap">
        {{-- Breadcrumbs --}}
        @include('filament-attachment-library::components.breadcrumbs')

        {{-- Filtering, sorting and header actions --}}
        @include('filament-attachment-library::components.header-actions')

    </div>

    {{-- Main attachment browser content --}}
    <div class="flex flex-row gap-4 mt-4">

        <div class="flex-1 flex flex-wrap gap-4 content-start">

            {{-- Empty directory notice --}}
            @if($this->paginator->isEmpty())
                @include('filament-attachment-library::components.empty-path-notice')
            @endif

            {{-- Attachment list & pagination --}}
            @if(! $this->paginator->isEmpty())
                <livewire:attachment-item-list :attachments="$this->paginator->getCollection()" />

                <x-filament::pagination :paginator="$this->paginator" extreme-links class="w-full"/>
            @endif

        </div>

        {{-- Show selected attachment metadata --}}
        @if(! $this->paginator->isEmpty())
            <livewire:attachment-info />
        @endif

    </div>

    <x-filament-actions::modals/>

</div>
