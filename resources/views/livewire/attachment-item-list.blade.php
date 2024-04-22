<div class="flex-1 flex flex-wrap gap-4 content-start w-full" x-data="{attachments: $wire.$entangle('attachments'), statePath: $wire.statePath, handleClick(item){
            if (item.type === 'attachment') {
                $store.attachmentBrowser?.isSelected(item.id, this.statePath)
                ? $store.attachmentBrowser?.deselect(item.id, this.statePath)
                : $store.attachmentBrowser?.select(item.id, this.statePath);
                return;
            }
            if (item.type === 'directory') {
                $store.attachmentBrowser?.openPath(item.fullPath, this.statePath)
                return;
            }
        } }">
    <template x-for="(attachment, index) in attachments" :key="index">
        <div @click="handleClick(attachment)"
             :class="{'dark:bg-white bg-black dark:text-black text-white': (attachment.type === 'attachment' && $store.attachmentBrowser?.isSelected(attachment.id, statePath))}"
             class="cursor-pointer relative flex flex-col dark:bg-gray-900 hover:bg-black hover:dark:bg-white hover:text-white hover:dark:text-black transition ease-in-out bg-white rounded-lg box-border basis-3/12 grow min-w-56 h-32"
        >
            <template x-if="attachment.type === 'attachment' && attachment.hasThumbnail">
                <img alt="" loading="lazy" :src="attachment.url" class="relative rounded-lg overflow-hidden h-full w-full object-center opacity-30 object-cover">
            </template>
            <div class="absolute p-6">
                <template x-if="attachment.type === 'directory'"><x-filament::icon icon="heroicon-o-folder" class="w-8 h-8 m-0" /></template>
                <template x-if="attachment.type === 'attachment' && attachment.hasThumbnail"><x-filament::icon icon="heroicon-o-photo" class="w-8 h-8 m-0" /></template>
                <template x-if="attachment.type === 'attachment' && !attachment.hasThumbnail"><x-filament::icon icon="heroicon-o-document" class="w-8 h-8 m-0" /></template>

                <p class="max-w-48 overflow-hidden box line-clamp-1 mt-2" x-text="attachment.name"></p>

                <p class="block text-sm font-medium" x-show="attachment.type === 'attachment'" x-text="attachment.size + ' MB'"></p>
            </div>

            <div class="absolute top-4 right-4" x-on:click.stop="">
                <x-filament::icon
                        icon="heroicon-o-check"
                        class="w-8 h-8 m-0"
                        x-show="attachment.type === 'attachment' && $store.attachmentBrowser?.isSelected(attachment.id, statePath)"
                />
            </div>
        </div>
    </template>
</div>
