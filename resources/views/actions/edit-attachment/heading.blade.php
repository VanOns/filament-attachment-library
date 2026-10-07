@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */

    $meta = array_filter([
        $attachment->extension,
        $attachment->size . ' MB',
        $attachment->createdAt ? __('filament-attachment-library::views.edit.' . ($attachment->createdBy ? 'uploaded' : 'uploaded_at'), [
            'date' => $attachment->createdAt->translatedFormat('j M Y'),
            'user' => $attachment->createdBy,
        ]) : null,
        $attachment->updatedAt && $attachment->createdAt && $attachment->updatedAt->ne($attachment->createdAt) ? __('filament-attachment-library::views.edit.' . ($attachment->updatedBy ? 'edited' : 'edited_at'), [
            'date' => $attachment->updatedAt->translatedFormat('j M Y'),
            'user' => $attachment->updatedBy,
        ]) : null,
    ]);

    [$type, $color] = match (true) {
        $attachment->isImage() => [__('filament-attachment-library::views.sidebar.mime_type.image'), 'primary'],
        $attachment->isVideo() => [__('filament-attachment-library::views.sidebar.mime_type.video'), 'primary'],
        default => [__('filament-attachment-library::views.edit.file'), 'gray'],
    };
@endphp

<span class="flex items-center gap-4 font-normal">
    <span @class([
        'flex size-10 shrink-0 items-center justify-center rounded-lg',
        'bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-400' => $color === 'primary',
        'bg-gray-100 text-gray-600 dark:bg-white/5 dark:text-gray-400' => $color === 'gray',
    ])>
        <x-filament::icon :icon="$attachment->icon()" class="size-5"/>
    </span>

    <span class="block min-w-0 flex-1">
        <span class="flex items-center gap-2">
            <span class="truncate text-base font-semibold text-gray-950 dark:text-white">{{ $attachment->name }}</span>
            <x-filament::badge :color="$color">{{ $type }}</x-filament::badge>
        </span>
        <span class="block truncate text-sm text-gray-500 dark:text-gray-400">{{ implode(' · ', $meta) }}</span>
    </span>

    <span class="flex shrink-0 items-center gap-2">
        <x-filament::icon-button
            icon="heroicon-o-arrow-top-right-on-square"
            color="gray"
            tag="a"
            :href="$attachment->url"
            target="_blank"
            :label="__('filament-attachment-library::views.actions.attachment.open')"
            :tooltip="__('filament-attachment-library::views.actions.attachment.open')"
        />
        {{-- The edit action's registered child actions, opened on top of the slide-over --}}
        <x-filament::icon-button
            icon="heroicon-o-arrow-path"
            color="gray"
            :label="__('filament-attachment-library::views.actions.attachment.replace')"
            :tooltip="__('filament-attachment-library::views.actions.attachment.replace')"
            wire:click="mountAction('replace')"
        />
        <x-filament::icon-button
            icon="heroicon-o-arrow-right-circle"
            color="gray"
            :label="__('filament-attachment-library::views.actions.attachment.move')"
            :tooltip="__('filament-attachment-library::views.actions.attachment.move')"
            wire:click="mountAction('move')"
        />
        <x-filament::icon-button
            icon="heroicon-o-trash"
            color="danger"
            :label="__('filament-attachment-library::views.actions.attachment.delete')"
            :tooltip="__('filament-attachment-library::views.actions.attachment.delete')"
            wire:click="mountAction('delete')"
        />
        <span class="ms-2 h-6 w-px bg-gray-200 dark:bg-white/10"></span>
    </span>
</span>
