<div {{-- Dispatch attachment browser loaded event --}}
     data-dispatch="attachment-browser-loaded"
     {{-- Load attachment browser javascript --}}
     x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('attachmentBrowser'))]">

    {{-- Filtering, sorting and header actions --}}
    <div class="flex justify-between align-center mb-4 items-center flex-wrap">
        @include('filament-attachment-library::components.breadcrumbs')
        <div class="min-w-full md:min-w-[initial]">
            @include('filament-attachment-library::components.header-actions')
            @include('filament-attachment-library::components.filters')
        </div>
    </div>

    {{-- Main attachment browser content --}}
    <div class="flex flex-row gap-4">

        {{-- Loading indicator for attachments --}}
        <div class="flex-1" wire:loading wire:target="openPath,sortBy,pageSize,search">
            <x-filament::loading-indicator class="h-8 w-8 mx-auto"/>
        </div>

        <div class="flex-1 flex flex-wrap gap-4 content-start" wire:loading.remove wire:target="openPath,sortBy,pageSize,search">

            {{-- Empty directory notice --}}
            @if($items->isEmpty())
                @include('filament-attachment-library::components.empty-path-notice')
            @endif

            {{-- Attachment list & pagination --}}
            @if(! $items->isEmpty())
                <livewire:attachment-item-list :attachments="$items->getCollection()" wire:key="attachment-item-list-{{ Str::random(10) }}" />

                <x-filament::pagination :paginator="$items" extreme-links class="w-full"/>
            @endif

        </div>

        {{-- Show info block for highlighted attachment --}}
        @if(! $items->isEmpty())
            <livewire:attachment-info :attachment="$highlightedAttachment" wire:key="attachment-info-{{ Str::random(10) }}" />
        @endif

    </div>

    <x-filament-actions::modals/>
</div>
