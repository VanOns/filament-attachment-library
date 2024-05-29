<div {{-- Dispatch attachment browser loaded event --}}
     data-dispatch="attachment-browser-loaded"
     {{-- Load attachment browser javascript --}}
     x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('attachmentBrowser'))]"
     x-data="{forms: {'uploadAttachment': false, 'createDirectory': false}}"
     x-on:dragover.prevent="forms.uploadAttachment = true"
     x-on:show-form.window="forms[$event.detail.form] = true"
     x-on:hide-form.window="forms[$event.detail.form] = false;">

    <div class="flex justify-between align-center mb-4 items-center flex-wrap">
        {{-- Breadcrumbs --}}
        @include('filament-attachment-library::components.breadcrumbs')

        {{-- Filtering, sorting and header actions --}}
        @include('filament-attachment-library::components.header-actions')
    </div>

    {{-- Popout forms for global actions --}}
    @include('filament-attachment-library::components.forms')

    {{-- Main attachment browser content --}}
    <div class="flex flex-row gap-4 mt-4 flex-wrap">

        {{-- Empty directory notice --}}
        @if($this->paginator->isEmpty())
            @include('filament-attachment-library::components.empty-path-notice')
        @endif

        {{-- Attachment list & pagination --}}
        @if(! $this->paginator->isEmpty())
            <livewire:attachment-item-list :attachments="$this->paginator->getCollection()" />

        @endif

        {{-- Show selected attachment metadata --}}
        @if(! $this->paginator->isEmpty())
            <livewire:attachment-info />
        @endif

        {{-- Pagination --}}
        <div class="mt-4 w-full">
            <x-filament::pagination :paginator="$this->paginator" extreme-links />
        </div>
    </div>

    <x-filament-actions::modals/>

</div>
