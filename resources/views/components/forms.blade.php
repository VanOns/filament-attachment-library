{{-- Upload attachment form --}}
<div x-show="forms['uploadAttachment']">
    <x-filament::card>
        <form wire:submit.prevent="saveUploadAttachmentForm">

            <h1>{{__('filament-attachment-library::forms.upload-attachment.heading')}}</h1>

            {{$this->uploadAttachmentForm}}

            <x-filament::button type="submit" class="mt-4">
                {{__('filament-attachment-library::views.actions.attachment.upload')}}
            </x-filament::button>

            <x-filament::button type="button" class="mt-4" color="gray" x-on:click="forms['uploadAttachment'] = false">
                {{__('filament-attachment-library::views.close')}}
            </x-filament::button>

        </form>
    </x-filament::card>
</div>

{{-- Create directory form --}}
<div x-show="forms['createDirectory']">
    <x-filament::card>
        <form wire:submit.prevent="saveCreateDirectoryForm">

            <h1 class="mb-2">{{__('filament-attachment-library::forms.create-directory.heading')}}</h1>

            {{$this->createDirectoryForm}}

            <x-filament::button type="submit" class="mt-4">
                {{__('filament-attachment-library::views.actions.directory.create')}}
            </x-filament::button>

            <x-filament::button type="button" class="mt-4" color="gray" x-on:click="forms['createDirectory'] = false">
                {{__('filament-attachment-library::views.close')}}
            </x-filament::button>

        </form>
    </x-filament::card>
</div>
