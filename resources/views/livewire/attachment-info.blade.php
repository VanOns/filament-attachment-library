<div x-data="{ attachment: $wire.$entangle('attachment').live }"
    class="p-6 flex-1 sticky top-24 w-full min-w-[400px] flex-grow-0 self-start rounded-l-xl bg-white dark:bg-gray-900 rounded-lg hidden md:block max-w-md">

    {{-- No attachment selected --}}
    <template x-if="typeof attachment !== 'undefined' && attachment === null">
        <div>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                <span class="break-words">{{ __('filament-attachment-library::views.info.empty.title') }}</span>
            </h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ __('filament-attachment-library::views.info.empty.description') }}
            </p>
        </div>
    </template>

    {{-- Attachment selected --}}
    <template x-if="typeof attachment !== 'undefined' && attachment !== null">
        <div>

            {{-- Preview/icon --}}
            <template x-if="attachment.is_image">
                <img loading="lazy" :src="attachment?.url" class="relative rounded-lg dark:opacity-80 focus-within:ring-2 focus-within:ring-offset-4 focus-within:ring-offset-gray-100 focus-within:ring-primary-600 h-full w-auto max-h-48 m-auto">
            </template>

            <template x-if="attachment.is_video">
                <video :src="attachment?.url" controls class="relative object-cover object-center rounded-lg dark:opacity-80 focus-within:ring-2 focus-within:ring-offset-4 focus-within:ring-offset-gray-100 focus-within:ring-primary-600 h-full w-full max-h-48">
                </video>
            </template>

            <template x-if="!attachment.is_image && !attachment.is_video">
                <x-filament::icon icon="heroicon-o-document" class="w-8 h-8" />
            </template>

            {{-- Details --}}
            <div class="mt-6">
                <h2 class="break-words text-xl font-medium text-gray-900 dark:text-gray-100" x-text="attachment.name"></h2>
                <div class="grid mt-2 grid-cols-2 gap-y-2">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.created_at') }}</p>
                    <p x-text="attachment.created_at"></p>

                    <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.mime_type') }}</p>
                    <p x-text="attachment.mime_type"></p>

                    <p class="flex-1 text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.path') }}</p>
                    <p class="flex-1" x-text="attachment.path ?? '/'"></p>

                    <p class="flex-1 text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.url') }}</p>
                    <p class="cursor-pointer break-all" x-clipboard="attachment.url">
                        <span x-text="attachment.url"></span>
                        <x-filament::icon icon="heroicon-o-document-duplicate" class="w-6 h-6 inline" />
                    </p>

                    <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.size') }}</p>
                    <p><span x-text="attachment.size"></span> MB</p>
                </div>

                <x-filament::section collapsible collapsed class="mt-4">
                    <x-slot name="heading">
                        {{ __('filament-attachment-library::views.info.details.meta') }}
                    </x-slot>

                    <div class="grid mt-2 grid-cols-2 gap-y-2">
                        <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.title') }}</p>
                        <p x-text="attachment.title ?? '-'"></p>

                        <p class="text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::views.info.details.description') }}</p>
                        <p x-text="attachment.description ?? '-'"></p>

                        <p class="text-gray-500 dark:text-gray-400" x-show="attachment.is_image">{{ __('filament-attachment-library::views.info.details.alt') }}</p>
                        <p x-text="attachment.alt ?? '-'" x-show="attachment.is_image"></p>

                        <p class="text-gray-500 dark:text-gray-400" x-show="attachment.is_image">{{ __('filament-attachment-library::views.info.details.caption') }}</p>
                        <p x-text="attachment.caption ?? '-'" x-show="attachment.is_image"></p>
                    </div>
                </x-filament::section>

            </div>

            {{-- Actions --}}
            <template x-if="$store.attachmentBrowser?.showActions()">
                <div class="mt-6">
                    <div class="grid grid-cols-1 gap-2 mt-2">
                        <x-filament::button color="gray" x-on:click="window.open(attachment.url)">
                            {{ __('filament-attachment-library::views.actions.attachment.open') }}
                        </x-filament::button>

                        <x-filament::button color="gray" x-on:click="$dispatch('mount-action', {name: 'editAttributeAttachmentAction', arguments: {'attachment_id': attachment.id}})">
                            {{ __('filament-attachment-library::views.actions.attachment.edit') }}
                        </x-filament::button>

                        <x-filament::button color="danger" x-on:click="$dispatch('mount-action', {name: 'deleteAttachment', arguments: {'attachment_id': attachment.id}})">
                            {{ __('filament-attachment-library::views.actions.attachment.delete') }}
                        </x-filament::button>
                    </div>
                </div>
            </template>

        </div>
    </template>

</div>
