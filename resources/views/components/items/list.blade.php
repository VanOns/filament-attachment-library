@php
    use VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel;
    use VanOns\FilamentAttachmentLibrary\ViewModels\DirectoryViewModel;

   /**
    * @var \Illuminate\Support\Collection<\VanOns\LaravelAttachmentLibrary\Models\Attachment> $attachments
    */
@endphp

@props(['attachments', 'selected'])

<div class="grid gap-4">
    @foreach($attachments as $viewModel)
        @if($viewModel instanceof \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel)
            <x-filament-attachment-library::attachment.list-item
                :attachment="$viewModel"
                :selected="$viewModel->isSelected($selected)"
                wire:click="selectAttachment({{ json_encode($viewModel->id) }})"
            >
                <x-slot name="actions">
                    <x-filament-attachment-library::attachment.browser-actions :attachment="$viewModel" />
                </x-slot>
            </x-filament-attachment-library::attachment.list-item>
        @endif

        @if($viewModel instanceof \VanOns\FilamentAttachmentLibrary\ViewModels\DirectoryViewModel)
            <x-filament-attachment-library::directory.list-item
                :directory="$viewModel"
                wire:click="openPath('{{ $viewModel->fullPath }}')"
            >
                <x-slot name="actions">
                    <x-filament-attachment-library::directory.browser-actions :directory="$viewModel" />
                </x-slot>
            </x-filament-attachment-library::directory.list-item>
        @endif
    @endforeach
</div>
