@props(['attachments', 'statePath', 'reorderable' => false, 'layout', 'multiple' => false, 'maxItems' => null, 'disabled' => false, 'valid' => true])

@php
    use VanOns\FilamentAttachmentLibrary\Enums\AttachmentFieldLayout;

    $compact = $layout === AttachmentFieldLayout::LIST;
    /**
     * @var \Illuminate\Support\Collection<\VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel> $attachments
     * @var bool $reorderable
     */
@endphp

<div>
    @if($layout === AttachmentFieldLayout::INPUT)
        <div
            @if($reorderable)
            x-data="attachmentSortable({ group: @js('attachments-' . $statePath) })"
            @endif
            class="flex flex-col gap-2"
        >
            @foreach($attachments as $attachment)
                <div data-attachment-id="{{ $attachment->id }}" class="min-w-0">
                    <x-filament-attachment-library::items.input-item
                        :attachment="$attachment"
                        :reorderable="$reorderable"
                        :disabled="$disabled"
                        :valid="$valid"
                    />
                </div>
            @endforeach
        </div>

        @if($attachments->isEmpty() || ($multiple && ! $disabled && (! $maxItems || $attachments->count() < $maxItems)))
            <x-filament-attachment-library::items.input-item
                :placeholder="__('filament-attachment-library::forms.attachment_field.' . ($attachments->isEmpty() ? 'choose' : 'add'))"
                :disabled="$disabled"
                :valid="$valid"
                @class(['mt-2' => $attachments->isNotEmpty()])
            />
        @endif
    @elseif($attachments->isEmpty())
        <p class="inline-block border-2 border-dashed border-gray-300 dark:border-gray-600 p-4 rounded-xl font-medium text-gray-900 dark:text-gray-100">{{ __('filament-attachment-library::forms.attachment_field.no_file_selected') }}</p>
    @else
        <div
            @if($reorderable)
            x-data="attachmentSortable({ group: @js('attachments-' . $statePath) })"
            @endif
            @class([
                'grid grid-cols-1 gap-2' => $compact,
                'grid grid-cols-[repeat(auto-fill,minmax(200px,1fr))] gap-4' => !$compact,
            ])
        >
            @foreach($attachments as $attachment)
                <div data-attachment-id="{{ $attachment->id }}" class="min-w-0">
                    @if($compact)
                        <x-filament-attachment-library::attachment.list-item
                            :attachment="$attachment"
                            :selected="false"
                            x-on:click="{{ $disabled ? '' : 'openBrowser(' . json_encode($attachment->id) . ')' }}"
                            class="{{ $disabled ? '' : 'cursor-pointer' }}"
                        >
                            <x-slot name="handle">
                                @if($reorderable)
                                    <button
                                        data-drag-handle
                                        type="button"
                                        class="cursor-grab me-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition"
                                        aria-label="{{ __('filament-attachment-library::views.field.drag_to_reorder') }}"
                                    >
                                        <x-filament::icon icon="heroicon-o-bars-2" class="size-4"/>
                                    </button>
                                @endif
                            </x-slot>

                            <x-slot name="actions">
                                <button
                                        class="p-1 text-gray-400 hover:text-danger-600 dark:text-gray-500 dark:hover:text-danger-400 transition"
                                        x-on:click="$dispatch('attachment-removed', { id: {{ json_encode($attachment->id) }} })" type="button"
                                >
                                    <x-filament::icon icon="heroicon-o-x-mark" class="size-5"/>
                                </button>
                            </x-slot>
                        </x-filament-attachment-library::attachment.list-item>
                    @else
                        <x-filament-attachment-library::attachment.grid-item
                            :attachment="$attachment"
                            x-on:click="{{ $disabled ? '' : 'openBrowser(' . json_encode($attachment->id) . ')' }}"
                            class="{{ $disabled ? '' : 'cursor-pointer' }}"
                        >
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
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
