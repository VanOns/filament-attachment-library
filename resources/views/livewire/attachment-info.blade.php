<div x-data="{ attachment: $wire.$entangle('attachment').live }"
    class="p-6 flex-1 sticky top-24 w-full min-w-[400px] flex-grow-0 self-start rounded-l-xl bg-white dark:bg-gray-900 rounded-lg hidden md:block max-w-md">

    {{-- No attachment selected --}}
    <template x-if="typeof attachment !== 'undefined' && attachment === null">
        <div>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                <span class="break-words">{{__('filament-attachment-library::views.info.empty.title')}}</span>
            </h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{__('filament-attachment-library::views.info.empty.description')}}
            </p>
        </div>
    </template>

    {{-- Attachment selected --}}
    <template x-if="typeof attachment !== 'undefined' && attachment !== null">
        <div>

            {{-- Preview/icon --}}
            <template x-if="attachment.hasThumbnail">
                <img loading="lazy" :src="attachment?.url" class="relative object-cover object-center rounded-lg dark:opacity-80 focus-within:ring-2 focus-within:ring-offset-4 focus-within:ring-offset-gray-100 focus-within:ring-primary-600 h-full w-full max-h-48">
            </template>

            <template x-if="!attachment.hasThumbnail">
                <x-filament::icon icon="heroicon-o-document" class="w-8 h-8" />
            </template>

            {{-- Details --}}
            <div class="mt-6">
                <h2 class="break-words text-xl font-medium text-gray-900 dark:text-gray-100" x-text="attachment.name"></h2>
                <div class="grid mt-2 grid-cols-2 gap-y-2">
                    <p class="text-gray-500 dark:text-gray-400">{{__('filament-attachment-library::views.info.details.size')}}</p>
                    <p><span x-text="attachment.size"></span> MB</p>

                    <p class="text-gray-500 dark:text-gray-400">{{__('filament-attachment-library::views.info.details.created_at')}}</p>
                    <p x-text="new Date(attachment.created_at).toLocaleString()"></p>

                    <p class="text-gray-500 dark:text-gray-400">{{__('filament-attachment-library::views.info.details.mime_type')}}</p>
                    <p x-text="attachment.mime_type"></p>

                    <p class="flex-1 text-gray-500 dark:text-gray-400">{{__('filament-attachment-library::views.info.details.path')}}</p>
                    <p class="flex-1" x-text="attachment.path ?? '/'"></p>

                    <p class="flex-1 text-gray-500 dark:text-gray-400">{{__('filament-attachment-library::views.info.details.url')}}</p>
                    <p class="cursor-pointer break-all">
                        <span x-text="attachment.url" x-clipboard></span>
                        <x-filament::icon icon="heroicon-o-document-duplicate" class="w-6 h-6 inline" />
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <template x-if="$store.attachmentBrowser?.showActions()">
                <div class="mt-6">
                    <div class="grid grid-cols-1 gap-2 mt-2">
                        <x-filament::button color="gray" x-on:click="window.open(attachment.url)">
                            {{__('filament-attachment-library::views.actions.attachment.open')}}
                        </x-filament::button>

                        <x-filament::button color="gray" x-on:click="$dispatch('mount-action', {name: 'renameAttachment', arguments: {'attachment_id': attachment.id}});">
                            {{__('filament-attachment-library::views.actions.attachment.rename')}}
                        </x-filament::button>

                        <x-filament::button color="danger" x-on:click="$dispatch('mount-action', {name: 'deleteAttachment', arguments: {'attachment_id': attachment.id}});">
                            {{__('filament-attachment-library::views.actions.attachment.delete')}}
                        </x-filament::button>
                    </div>
                </div>
            </template>

        </div>
    </template>

</div>
