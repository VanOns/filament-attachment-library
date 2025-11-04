@php
    use VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel;
    use VanOns\FilamentAttachmentLibrary\ViewModels\DirectoryViewModel;

   /**
    * @var \Illuminate\Support\Collection<\VanOns\LaravelAttachmentLibrary\Models\Attachment> $attachments
    */
@endphp

@props([ 'attachments', 'selected' ])

<div class="grid grid-cols-[repeat(auto-fill,minmax(200px,1fr))] gap-4">
    @foreach($attachments as $viewModel)
        @if($viewModel instanceof AttachmentViewModel)
            <x-filament-attachment-library::attachment.grid-item
                :attachment="$viewModel"
                :selected="$viewModel->isSelected($selected)"
                wire:click="selectAttachment({{ json_encode($viewModel->id) }})"
            >
                <x-slot name="actions">
                    <x-filament-attachment-library::attachment.browser-actions
                        :attachment="$viewModel"
                        trigger-class="p-1 bg-white dark:bg-black shadow-xs rounded-md border border-black/10 dark:border-white/10 opacity-0 group-hover:opacity-100 transition"
                    />
                </x-slot>
            </x-filament-attachment-library::attachment.grid-item>
        @endif

        @if($viewModel instanceof DirectoryViewModel)
            <x-filament-attachment-library::directory.grid-item
                :directory="$viewModel"
                wire:click="openPath('{{ $viewModel->fullPath }}')"
            >
                <x-slot name="actions">
                    <x-filament-attachment-library::directory.browser-actions
                        :directory="$viewModel"
                        trigger-class="p-1 bg-white dark:bg-black shadow-xs rounded-md border border-black/10 dark:border-white/10 opacity-0 group-hover:opacity-100 transition"
                    />
                </x-slot>
            </x-filament-attachment-library::directory.grid-item>
        @endif
    @endforeach
</div>
