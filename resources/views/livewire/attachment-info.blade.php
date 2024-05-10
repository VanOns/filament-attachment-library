<div
    x-data="{attachment: $wire.$entangle('attachment').live}"
    class="p-6 flex-1 sticky top-24 w-full min-w-[400px] flex-grow-0 self-start rounded-l-xl bg-white dark:bg-gray-900 rounded-lg hidden md:block max-w-md">

    <x-filament::loading-indicator wire:loading wire:target="openPath" class="h-8 w-8 mx-auto" />

    <template x-if="typeof attachment === 'undefined' || attachment === null">
        <div wire:loading.remove wire:target="openPath">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                <span class="break-words">Selecteer een map of bestand</span>
            </h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Selecteer een bestand om de informatie te bekijken.
            </p>
        </div>
    </template>

    <template x-if="typeof attachment !== 'undefined' && attachment !== null">
        <div wire:loading.remove wire:target="openPath">

            <template x-if="attachment.hasThumbnail">
                <img loading="lazy" :src="attachment?.url" class="relative object-cover object-center rounded-lg dark:opacity-80 focus-within:ring-2 focus-within:ring-offset-4 focus-within:ring-offset-gray-100 focus-within:ring-primary-600 h-full w-full max-h-48">
            </template>

            <template x-if="!attachment.hasThumbnail">
                <x-filament::icon icon="heroicon-o-document" class="w-8 h-8" />
            </template>

            <div class="mt-6">
                <h2 class="break-words text-xl font-medium text-gray-900 dark:text-gray-100" x-text="attachment.name">
                </h2>
                <div class="grid mt-2 grid-cols-2 gap-y-2">
                    <p class="text-gray-500 dark:text-gray-400">Bestandsgrootte</p>
                    <p><span x-text="attachment.size"></span> MB</p>

                    <p class="text-gray-500 dark:text-gray-400">Geüpload op</p>
                    <p x-text="new Date(attachment.created_at).toLocaleString()"></p>

                    <p class="text-gray-500 dark:text-gray-400">MIME-type</p>
                    <p x-text="attachment.mime_type"></p>

                    <p class="flex-1 text-gray-500 dark:text-gray-400">Pad</p>
                    <p class="flex-1" x-text="attachment.path ?? '/'"></p>

                    <p class="flex-1 text-gray-500 dark:text-gray-400">Link</p>
                    <p class="cursor-pointer break-all">
                        <span x-text="attachment.url" x-clipboard></span>
                        <x-filament::icon icon="heroicon-o-document-duplicate" class="w-6 h-6 inline" />
                    </p>
                </div>
            </div>

            <template x-if="$store.attachmentBrowser?.showActions()">
                <div class="mt-6">
                    <div class="grid grid-cols-1 gap-2 mt-2">
                        <x-filament::button color="gray" x-on:click="window.open(attachment.url)">
                            Open attachment
                        </x-filament::button>

                        <x-filament::button color="gray" x-on:click="$dispatch('mount-action', {name: 'renameAttachment', arguments: {'attachment_id': attachment.id}});">
                            Rename attachment
                        </x-filament::button>

                        <x-filament::button color="danger" x-on:click="$dispatch('mount-action', {name: 'deleteAttachment', arguments: {'attachment_id': attachment.id}});">
                            Delete attachment
                        </x-filament::button>
                    </div>
                </div>
            </template>

        </div>
    </template>

</div>
