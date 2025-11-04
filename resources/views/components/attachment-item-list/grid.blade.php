@php
    /**
     * @var \Illuminate\Support\Collection<\VanOns\LaravelAttachmentLibrary\Models\Attachment> $attachments
     */
@endphp

@props([ 'attachments', 'selected' ])

<div class="grid grid-cols-[repeat(auto-fill,minmax(200px,1fr))] gap-4">
    @foreach($attachments as $viewModel)
        @if($viewModel instanceof \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel)
            <x-filament-attachment-library::attachment.grid-item :attachment="$viewModel" :selected="$viewModel->isSelected($selected)">
                <x-slot name="actions">
                    <x-filament-attachment-library::attachment.browser-actions :attachment="$viewModel" trigger-class="opacity-0 group-hover:opacity-100 transition" />
                </x-slot>
            </x-filament-attachment-library::attachment.grid-item>
        @endif

        @if($viewModel instanceof \VanOns\FilamentAttachmentLibrary\ViewModels\DirectoryViewModel)
            <x-filament-attachment-library::directory.grid-item :directory="$viewModel">
                <x-slot name="actions">
                    <x-filament-attachment-library::directory.browser-actions :directory="$viewModel" trigger-class="opacity-0 group-hover:opacity-100 transition" />
                </x-slot>
            </x-filament-attachment-library::directory.grid-item>
        @endif
    @endforeach
</div>
