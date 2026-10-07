@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
    $lines = 10;
    $preview = $attachment->textPreview($lines);
@endphp

<div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="flex h-40 flex-col items-center justify-center gap-3 bg-gray-50 text-gray-500 dark:bg-white/5 dark:text-gray-400">
        <x-filament::icon icon="heroicon-o-document-text" class="size-12"/>
        <x-filament::badge color="gray">{{ $attachment->extension }}</x-filament::badge>
    </div>

    @if($preview !== null)
        <div class="border-t border-gray-100 p-4 dark:border-white/10">
            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.edit.preview') }}</p>
            <pre class="overflow-x-auto rounded-lg bg-gray-50 p-3 font-mono text-xs leading-5 text-gray-700 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:text-gray-300 dark:ring-white/10">{{ $preview }}</pre>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.edit.preview_lines', ['count' => $lines]) }}</p>
        </div>
    @endif
</div>
