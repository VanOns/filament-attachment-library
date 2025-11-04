@props([ 'attachments', 'statePath'])

@php
    use VanOns\LaravelAttachmentLibrary\Facades\Glide;
    use VanOns\LaravelAttachmentLibrary\Facades\Resizer;
    /**
     * @var \Illuminate\Support\Collection<\VanOns\LaravelAttachmentLibrary\Models\Attachment> $attachments
     */
@endphp

<div>
    @if($attachments->isEmpty())
        <p class="inline-block border-2 border-dashed border-gray-300 dark:border-gray-600 p-4 rounded-xl font-medium text-gray-900 dark:text-gray-100">{{ __('filament-attachment-library::forms.attachment_field.no_file_selected') }}</p>
    @else
        <div class="grid grid-cols-[repeat(auto-fill,minmax(200px,1fr))] gap-4">
            @foreach($attachments as $attachment)
                <x-filament-attachment-library::attachment.grid-item :attachment="$attachment">
                    <x-slot name="actions">
                        <button
                            class="opacity-0 group-hover:opacity-100 transition"
                            x-on:click="$dispatch('attachment-removed', { id: {{ json_encode($attachment->id) }} })" type="button"
                        >
                            <x-filament::icon icon="heroicon-o-x-circle" class="size-8 text-white"/>
                        </button>
                    </x-slot>
                </x-filament-attachment-library::attachment.grid-item>
            @endforeach
        </div>
    @endif
</div>
