@props([
    'inModal' => false,
])

<div
    @click="$store.attachmentBrowser?.handleItemClick(attachment, statePath)"
    x-on:contextmenu="$event.preventDefault(); $el.querySelector('.toggle')?.parentNode?.click(); return false"
    :class="attachment.class === 'attachment' && $store.attachmentBrowser?.isSelected(attachment.id, statePath)
        ? 'bg-black dark:bg-gray-300 dark:text-white text-white hover:dark:bg-red-400 hover:dark:text-white'
        : 'bg-white dark:bg-gray-{{ $inModal ? '800' : '900' }} hover:bg-gray-{{ $inModal ? '800' : '900' }} hover:dark:bg-gray-300 hover:text-white hover:dark:text-black'"
    @class([
        'cursor-pointer relative flex flex-row items-center justify-between h-8 w-full px-4 py-6 transition ease-in-out box-border group',
        'rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
    ])
>
    {{-- Attachment item icon --}}
    <div class="flex items-center gap-x-2">
        <p x-text="attachment.name" class="max-w-48 overflow-hidden box line-clamp-1"></p>

        <p
            class="block text-sm font-medium opacity-60"
            x-show="attachment.class === 'attachment'"
            x-text="attachment.size + ' MB'"
        ></p>
    </div>

    {{-- Attachment item actions & hover indicator --}}
    <x-filament-attachment-library::attachment-item-list.attachment-actions />
</div>
