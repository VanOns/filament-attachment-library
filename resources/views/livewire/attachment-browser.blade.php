@php
    use VanOns\FilamentAttachmentLibrary\Enums\Layout
@endphp

<div>
    <div class="flex justify-between align-center mb-6 items-center flex-wrap">
        <x-filament-attachment-library::breadcrumbs/>
        <x-filament-attachment-library::header-actions :$layout/>
        <x-filament-attachment-library::header-actions-mobile :$layout/>
    </div>

    <div wire:key="attachment-search-heading">
        @if($search)
            <h1>{{ __('filament-attachment-library::views.browser.search_results') }} <span>{{ $search }}</span></h1>
        @endif
    </div>

    <div class="flex flex-col gap-6 mt-4 flex-wrap md:flex-row">
        <div
            @class([
                'flex-1 order-2 md:order-1',
                'opacity-50 pointer-events-none' => $disabled,
            ])
        >
            @if(!$directories->isEmpty())
                <x-filament-attachment-library::items.container :layout="$layout">
                    @foreach($directories as $directory)
                        <div wire:key="directory-browser-item-{{ md5($directory->fullPath) }}">
                            <x-filament-attachment-library::directory.browser-item
                                :$directory
                                :layout="$layout"
                            />
                        </div>
                    @endforeach
                </x-filament-attachment-library::items.container>

                <div class="w-full border-t border-gray-300 dark:border-gray-700 my-6"></div>
            @endif

            @if(!$attachments->isEmpty())
                <x-filament-attachment-library::items.container :layout="$layout">
                    @foreach($attachments as $attachment)
                        <div wire:key="attachment-browser-item-{{ $attachment->id }}">
                            <x-filament-attachment-library::attachment.browser-item
                                :$attachment
                                :layout="$layout"
                                :selected="$attachment->isSelected($selected)"
                            />
                        </div>
                    @endforeach
                </x-filament-attachment-library::items.container>
            @endif

            @if($attachments->isEmpty() && $directories->isEmpty())
                <x-filament-attachment-library::empty-path-notice :$currentPath/>
            @endif
        </div>

        <x-filament-attachment-library::sidebar :$selected :$currentPath :$disableMimeFilter class="order-1 md:order-2"/>

        <div class="mt-4 w-full order-3">
            <x-filament::pagination :paginator="$attachments" extreme-links/>
        </div>
    </div>

    <x-filament-actions::modals/>
</div>
