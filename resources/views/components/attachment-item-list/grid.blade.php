<template x-data="attachmentItemList" x-for="(attachment, index) in attachments" :key="attachment.id">

    <div
        x-data="attachmentItem({ attachment })"
        :class="isAttachment && isSelected
            ? 'bg-black dark:bg-gray-300 dark:text-black text-white hover:dark:bg-red-400 hover:dark:text-white'
            : 'dark:bg-gray-900 hover:bg-gray-900 hover:dark:bg-gray-300 hover:text-white hover:dark:text-black'"
        @class([
            'cursor-pointer relative flex flex-col min-w-[10rem] h-32 transition ease-in-out box-border basis-3/12 grow group',
            'rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10',
        ])
    >

        {{-- Preview image if attachment is image --}}
        <template x-if="isAttachment && isImage">
            <img
                alt="attachment.alt"
                loading="lazy"
                width="auto"
                height="100%"
                :src="attachment.thumbnail_url"
                class="relative rounded-lg overflow-hidden h-full w-full object-center opacity-60 object-cover"
            >
        </template>

        {{-- Attachment item icon --}}
        <div class="absolute p-6">
            <template x-if="isDirectory"><x-filament::icon icon="heroicon-o-folder" class="w-8 h-8 m-0" /></template>
            <template x-if="isAttachment && isImage"><x-filament::icon icon="heroicon-o-photo" class="w-8 h-8 m-0" /></template>
            <template x-if="isAttachment && isVideo"><x-filament::icon icon="heroicon-o-film" class="w-8 h-8 m-0" /></template>
            <template x-if="isAttachment && (!isImage && !isVideo)"><x-filament::icon icon="heroicon-o-document" class="w-8 h-8 m-0" /></template>

            <p x-text="attachment.name" class="max-w-48 overflow-hidden box line-clamp-1 mt-2"></p>

            <p class="block text-sm font-medium" x-show="isAttachment" x-text="attachment.size + ' MB'"></p>
        </div>

        {{-- Attachment item actions & hover indicator --}}
        <x-filament-attachment-library::attachment-item-list.attachment-actions class="absolute top-4 right-4" />

    </div>

</template>
