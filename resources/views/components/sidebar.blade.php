@props([
    'class' => '',
    'currentPath' => null,
    'disableMimeFilter' => false,
])

<div @class([ 'flex-1 max-w-md', $class ])>

    {{-- Upload attachment section --}}
    <x-filament::section collapsible collapsed class="mb-4" collapse-id="upload-attachment-form">

        <x-slot name="heading">
            <x-filament::icon
                class="inline w-6 h-6 text-primary-400 mr-2"
                icon="heroicon-o-document-plus"
                tooltip="{{ __('filament-attachment-library::views.actions.directory.create') }}"
            />
            {{ __('filament-attachment-library::forms.upload_attachment.heading') }}
        </x-slot>

        <form wire:submit.prevent="saveUploadAttachmentForm">
            {{$this->uploadAttachmentForm}}

            <div class="flex gap-4 mt-4">
                <x-filament::button type="submit">
                    {{ __('filament-attachment-library::views.actions.attachment.upload') }}
                </x-filament::button>

                <x-filament::button color="gray" x-on:click="$dispatch('collapse-section', {id: 'upload-attachment-form'})">
                    {{ __('filament-attachment-library::views.close') }}
                </x-filament::button>
            </div>
        </form>

    </x-filament::section>

    {{-- Create directory section --}}
    <x-filament::section collapsible collapsed class="mb-4" collapse-id="create-directory-form">

        <x-slot name="heading">
            <x-filament::icon
                class="inline w-6 h-6 text-primary-400 mr-2"
                icon="heroicon-o-folder-plus"
                tooltip="{{ __('filament-attachment-library::views.actions.directory.create') }}"
            />
            {{ __('filament-attachment-library::forms.create_directory.heading') }}
        </x-slot>

        <form wire:submit.prevent="saveCreateDirectoryForm">

            {{$this->createDirectoryForm}}

            <div class="flex gap-4 mt-4">
                <x-filament::button type="submit">
                    {{ __('filament-attachment-library::views.actions.directory.create') }}
                </x-filament::button>

                <x-filament::button color="gray" x-on:click="$dispatch('collapse-section', {id: 'create-directory-form'})">
                    {{ __('filament-attachment-library::views.close') }}
                </x-filament::button>
            </div>

        </form>
    </x-filament::section>

    @if(!$disableMimeFilter)
        {{-- Filter section --}}
        <x-filament::section collapsible collapsed class="mb-4" collapse-id="filter-form">

            <x-slot name="heading">
                <x-filament::icon
                        class="inline w-6 h-6 text-primary-400 mr-2"
                        icon="heroicon-o-funnel"
                        tooltip="{{ __('filament-attachment-library::views.actions.directory.create') }}"
                />
                {{ __('filament-attachment-library::views.sidebar.filters.header') }}
            </x-slot>

            {{-- Mime-type --}}
            <x-filament-forms::field-wrapper label="{{ __('filament-attachment-library::views.sidebar.filters.mime') }}">
                <x-filament::input.wrapper class="flex-1 min-w-full md:min-w-[initial]">
                    <x-filament::input.select wire:model.live="mime">

                        @foreach(\VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser::FILTERABLE_FILE_TYPES as $type => $mime)
                            <option value="{{$mime}}">{{__("filament-attachment-library::views.sidebar.mime_type.{$type}")}}</option>
                        @endforeach

                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-filament-forms::field-wrapper>

            <x-filament::button color="gray" class="mt-4" x-on:click="$dispatch('collapse-section', {id: 'filter-form'})">
                {{ __('filament-attachment-library::views.close') }}
            </x-filament::button>

        </x-filament::section>
    @endif

    {{-- Attachment info section --}}
    <livewire:attachment-info :currentPath="$currentPath" class="hidden md:block" />
    <livewire:attachment-info-modal :currentPath="$currentPath" />
</div>
