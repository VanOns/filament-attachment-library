<div class="flex-1 flex flex-wrap gap-4 content-start w-full" x-data="{attachments: $wire.$entangle('attachments'), statePath: $wire.statePath }">

    {{-- Empty directory notice --}}
    <template x-if="attachments?.length === 0">
        @include('filament-attachment-library::components.empty-path-notice')
    </template>

    <template x-if="typeof attachments !== undefined">

            <template x-for="(attachment, index) in attachments">
                {{-- Attachment item --}}
                <div @click="$store.attachmentBrowser?.handleItemClick(attachment, statePath)"
                     x-on:contextmenu="$event.preventDefault(); $el.querySelector('.toggle').parentNode.click(); return false"
                     :class="attachment.class === 'attachment' && $store.attachmentBrowser?.isSelected(attachment.id, statePath)
                    ? 'bg-black dark:bg-gray-300 dark:text-black text-white hover:dark:bg-red-400 hover:dark:text-white'
                    : 'dark:bg-gray-900 hover:bg-gray-900 hover:dark:bg-gray-300 hover:text-white hover:dark:text-black'"
                     class="cursor-pointer relative flex flex-col transition ease-in-out rounded-lg box-border basis-3/12 grow min-w-[10rem] h-32 group">
                    {{-- Preview image if attachment is image --}}
                    <template x-if="attachment.class === 'attachment' && attachment.is_image">
                        <img alt="attachment.alt" loading="lazy" width="auto" height="100%" :src="attachment.thumbnail_url" class="relative rounded-lg overflow-hidden h-full w-full object-center opacity-60 object-cover">
                    </template>

                    {{-- Attachment item icon --}}
                    <div class="absolute p-6">
                        <template x-if="attachment.class === 'directory'"><x-filament::icon icon="heroicon-o-folder" class="w-8 h-8 m-0" /></template>
                        <template x-if="attachment.class === 'attachment' && attachment.is_image"><x-filament::icon icon="heroicon-o-photo" class="w-8 h-8 m-0" /></template>
                        <template x-if="attachment.class === 'attachment' && attachment.is_video"><x-filament::icon icon="heroicon-o-film" class="w-8 h-8 m-0" /></template>
                        <template x-if="attachment.class === 'attachment' && (!attachment.is_image && !attachment.is_video)"><x-filament::icon icon="heroicon-o-document" class="w-8 h-8 m-0" /></template>

                        <p class="max-w-48 overflow-hidden box line-clamp-1 mt-2" x-text="attachment.name"></p>

                        <p class="block text-sm font-medium" x-show="attachment.class === 'attachment'" x-text="attachment.size + ' MB'"></p>
                    </div>

                    {{-- Attachment item actions & hover indicator --}}
                    <div class="absolute top-4 right-4" x-on:click.stop="">
                        <x-filament::dropdown placement="bottom-end" x-show="$store.attachmentBrowser?.showActions(statePath)">
                            <x-slot name="trigger">
                                <x-filament::icon
                                        x-show="
                                (attachment.class === 'attachment' && !$store.attachmentBrowser?.isSelected(attachment.id, statePath))
                                || (attachment.class === 'directory')"
                                        icon="heroicon-o-ellipsis-vertical"
                                        class="w-8 h-8 m-0 toggle"
                                />
                            </x-slot>

                            <x-filament::dropdown.list x-show="attachment.class === 'attachment'">
                                <x-filament::dropdown.list.item x-on:click="window.open(attachment.url)">
                                    {{ __('filament-attachment-library::views.actions.attachment.open') }}
                                </x-filament::dropdown.list.item>

                                <x-filament::dropdown.list.item x-on:click="$dispatch('mount-action', {name: 'editAttachmentAction', arguments: {'attachment_id': attachment.id}})">
                                    {{ __('filament-attachment-library::views.actions.attachment.edit') }}
                                </x-filament::dropdown.list.item>

                                <x-filament::dropdown.list.item color="danger" x-on:click="$dispatch('mount-action', {name: 'deleteAttachment', arguments: {'attachment_id': attachment.id}})">
                                    {{ __('filament-attachment-library::views.actions.attachment.delete') }}
                                </x-filament::dropdown.list.item>
                            </x-filament::dropdown.list>

                            <x-filament::dropdown.list x-show="attachment.class === 'directory'">
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
                                class="w-8 h-8 m-0 block group-hover:hidden"
                                x-show="attachment.class === 'attachment' && $store.attachmentBrowser?.isSelected(attachment.id, statePath)"
                        />
                        <x-filament::icon
                                icon="heroicon-o-x-circle"
                                class="w-8 h-8 m-0 hidden group-hover:block"
                                x-show="attachment.class === 'attachment' && $store.attachmentBrowser?.isSelected(attachment.id, statePath)"
                        />
                    </div>
                </div>

            </template>

    </template>
</div>
