<div class="flex-1 flex flex-wrap gap-4 content-start w-full" x-data="{attachments: $wire.$entangle('attachments'), statePath: $wire.statePath }">
    <template x-if="typeof attachments !== undefined">
        <template x-for="(attachment, index) in attachments">
            <div @click="$store.attachmentBrowser?.handleItemClick(attachment, statePath)"
                 x-on:contextmenu="$event.preventDefault(); $el.querySelector('.toggle').parentNode.click(); return false"
                 :class="{'bg-black dark:bg-white dark:text-black text-white': (attachment?.class === 'attachment' && $store.attachmentBrowser?.isSelected(attachment.id, statePath))}"
                 class="cursor-pointer relative flex flex-col dark:bg-gray-900 hover:bg-black hover:dark:bg-white hover:text-white hover:dark:text-black transition ease-in-out dark:bg-white rounded-lg box-border basis-3/12 grow min-w-[10rem] h-32">

                <template x-if="attachment?.class === 'attachment' && attachment.is_image">
                    <img alt="" loading="lazy" width="auto" height="100%" :src="attachment.url" class="relative rounded-lg overflow-hidden h-full w-full object-center opacity-30 object-cover">
                </template>

                <div class="absolute p-6">
                    <template x-if="attachment?.class === 'directory'"><x-filament::icon icon="heroicon-o-folder" class="w-8 h-8 m-0" /></template>
                    <template x-if="attachment?.class === 'attachment' && attachment.is_image"><x-filament::icon icon="heroicon-o-photo" class="w-8 h-8 m-0" /></template>
                    <template x-if="attachment?.class === 'attachment' && attachment.is_video"><x-filament::icon icon="heroicon-o-film" class="w-8 h-8 m-0" /></template>
                    <template x-if="attachment?.class === 'attachment' && (!attachment.is_image && !attachment.is_video)"><x-filament::icon icon="heroicon-o-document" class="w-8 h-8 m-0" /></template>

                    <p class="max-w-48 overflow-hidden box line-clamp-1 mt-2" x-text="attachment.name"></p>

                    <p class="block text-sm font-medium" x-show="attachment?.class === 'attachment'" x-text="attachment.size + ' MB'"></p>
                </div>

                <div class="absolute top-4 right-4" x-on:click.stop="">
                    <x-filament::dropdown placement="bottom-end" x-show="$store.attachmentBrowser?.showActions(statePath)">
                        <x-slot name="trigger">
                            <x-filament::icon
                                x-show="
                                (attachment?.class === 'attachment' && !$store.attachmentBrowser?.isSelected(attachment.id, statePath))
                                || (attachment?.class === 'directory')"
                                icon="heroicon-o-ellipsis-vertical"
                                class="w-8 h-8 m-0 toggle"
                            />
                        </x-slot>

                        <x-filament::dropdown.list x-show="attachment?.class === 'attachment'">
                            <x-filament::dropdown.list.item x-on:click="window.open(attachment.url)">
                                {{ __('filament-attachment-library::views.actions.attachment.open') }}
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item x-on:click="$dispatch('mount-action', {name: 'renameAttachment', arguments: {'attachment_id': attachment.id}})">
                                {{ __('filament-attachment-library::views.actions.attachment.rename') }}
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item color="danger" x-on:click="$dispatch('mount-action', {name: 'deleteAttachment', arguments: {'attachment_id': attachment.id}})">
                                {{ __('filament-attachment-library::views.actions.attachment.delete') }}
                            </x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>

                        <x-filament::dropdown.list x-show="attachment?.class === 'directory'">
                            <x-filament::dropdown.list.item x-on:click="$dispatch('mount-action', {name: 'renameDirectory', arguments: {'directory': attachment}})">
                                {{ __('filament-attachment-library::views.actions.directory.rename') }}
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item color="danger" x-on:click="$dispatch('mount-action', {name: 'deleteDirectory', arguments: {'directory': attachment}})">
                                {{ __('filament-attachment-library::views.actions.directory.delete') }}
                            </x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>

                    </x-filament::dropdown>

                    <x-filament::icon
                        icon="heroicon-o-check"
                        class="w-8 h-8 m-0"
                        x-show="attachment?.class === 'attachment' && $store.attachmentBrowser?.isSelected(attachment.id, statePath)"
                    />
                </div>
            </div>
        </template>
    </template>
</div>
