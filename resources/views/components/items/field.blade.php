@props(['attachments', 'statePath', 'reorderable' => false])

@php
    use VanOns\LaravelAttachmentLibrary\Facades\Glide;
    use VanOns\LaravelAttachmentLibrary\Facades\Resizer;
    /**
     * @var \Illuminate\Support\Collection<\VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel> $attachments
     * @var bool $reorderable
     */
@endphp

<div>
    @if($attachments->isEmpty())
        <p class="inline-block border-2 border-dashed border-gray-300 dark:border-gray-600 p-4 rounded-xl font-medium text-gray-900 dark:text-gray-100">{{ __('filament-attachment-library::forms.attachment_field.no_file_selected') }}</p>
    @else
        <div
            @if($reorderable)
            x-data="{
                init() {
                    if (!window.Sortable) { return; }

                    // Stop the SortableJS 'end' event from bubbling to Filament components that also sort (e.g. repeaters)
                    this.$el.addEventListener('end', (e) => {
                        e.stopPropagation();
                    }, true);

                    new window.Sortable(this.$el, {
                        animation: 150,
                        draggable: '[data-attachment-id]',
                        handle: '[data-drag-handle]',
                        ghostClass: 'opacity-50',
                        group: 'attachments-{{ $statePath }}',
                        onEnd: (event) => {
                            const ids = Array.from(
                                this.$el.querySelectorAll('[data-attachment-id]')
                            ).map(el => Number(el.dataset.attachmentId));
                            $dispatch('attachment-reordered', { ids });
                        }
                    });
                }
            }"
            @endif
            class="grid grid-cols-[repeat(auto-fill,minmax(200px,1fr))] gap-4"
        >
            @foreach($attachments as $attachment)
                <div data-attachment-id="{{ $attachment->id }}">
                    <x-filament-attachment-library::attachment.grid-item :attachment="$attachment">
                        <x-slot name="actions">
                            <div @class([
                                'flex-1 flex gap-1 justify-between' => $reorderable
                            ])>
                                @if($reorderable)
                                    <button
                                        data-drag-handle
                                        class="p-1 bg-white dark:bg-black shadow-xs rounded-md border border-black/10 dark:border-white/10 opacity-0 group-hover:opacity-100 transition cursor-grab"
                                        type="button"
                                        aria-label="{{ __('filament-attachment-library::views.field.drag_to_reorder') }}"
                                    >
                                        <x-filament::icon icon="heroicon-o-bars-2" class="size-6"/>
                                    </button>
                                @endif
                                <button
                                        class="p-1 bg-white dark:bg-black shadow-xs rounded-md border border-black/10 dark:border-white/10 opacity-0 group-hover:opacity-100 transition"
                                        x-on:click="$dispatch('attachment-removed', { id: {{ json_encode($attachment->id) }} })" type="button"
                                >
                                    <x-filament::icon icon="heroicon-o-x-mark" class="size-6"/>
                                </button>
                            </div>
                        </x-slot>
                    </x-filament-attachment-library::attachment.grid-item>
                </div>
            @endforeach
        </div>
    @endif
</div>
