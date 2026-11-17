@props(['tree', 'collapsed' => false])

@php
    /**
     * @var array{nodes: array<int, array<string, mixed>>, rootCount: int} $tree
     * @var bool $collapsed
     */
@endphp

{{-- On a narrow screen the tree is a block right above the files; beside them from md up. --}}
<div
    {{ $attributes->class(['order-2 w-full shrink-0 md:order-none md:w-auto md:self-start md:sticky md:top-[var(--fal-info-top,6rem)]']) }}
    x-data="attachmentDirectoryTree({
        expanded: $wire.entangle('expandedDirectories'),
        collapsed: $wire.entangle('treeCollapsed'),
        rootName: @js(__('filament-attachment-library::views.home')),
    })"
>
    {{-- Collapsed: only the buttons, with the toggle where it sits in the open tree.
         Filament pulls icon buttons outwards with a negative margin; fal-tree-button gives them their own room.
         Collapsing is for md and up, so it is a class there rather than x-show. --}}
    <div
        @class([
            'hidden flex-col gap-1 rounded-xl bg-white p-1.5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
            'md:flex' => $collapsed,
        ])
        x-bind:class="{ 'md:flex': collapsed }"
    >
        <x-filament::icon-button
            icon="heroicon-m-chevron-double-right"
            color="gray"
            class="fal-tree-button"
            x-ref="expandButton"
            :label="__('filament-attachment-library::views.tree.expand')"
            :tooltip="__('filament-attachment-library::views.tree.expand')"
            x-on:click="setCollapsed(false)"
        />

        <x-filament::icon-button
            icon="heroicon-o-folder-plus"
            color="gray"
            class="fal-tree-button"
            :label="__('filament-attachment-library::views.actions.directory.create')"
            :tooltip="__('filament-attachment-library::views.actions.directory.create')"
            wire:click="mountAction('createDirectory')"
        />
    </div>

    <nav
        aria-label="{{ __('filament-attachment-library::views.tree.heading') }}"
        @class([
            'relative flex w-full flex-col gap-2 rounded-xl bg-white px-2 py-1.5 shadow-sm ring-1 ring-gray-950/5 md:w-64 md:pb-3 dark:bg-gray-900 dark:ring-white/10',
            'md:hidden' => $collapsed,
        ])
        x-bind:class="{ 'md:hidden': collapsed }"
    >
        <div class="-ms-0.5 flex items-center gap-1.5">
            <div class="hidden md:flex">
                <x-filament::icon-button
                    icon="heroicon-m-chevron-double-left"
                    color="gray"
                    class="fal-tree-button"
                    x-ref="collapseButton"
                    :label="__('filament-attachment-library::views.tree.collapse')"
                    :tooltip="__('filament-attachment-library::views.tree.collapse')"
                    x-on:click="setCollapsed(true)"
                />
            </div>

            <span class="hidden flex-1 text-sm font-semibold text-gray-950 md:block dark:text-white">
                {{ __('filament-attachment-library::views.tree.heading') }}
            </span>

            {{-- Narrow screens: the heading opens the tree, and names the open directory while it is closed --}}
            <button
                type="button"
                class="flex min-h-11 min-w-0 flex-1 items-center gap-2 rounded-lg px-1.5 text-start text-sm font-semibold text-gray-950 md:hidden dark:text-white"
                x-on:click="toggleNarrow()"
                x-bind:aria-expanded="narrowOpen"
            >
                <x-filament::icon
                    icon="heroicon-m-chevron-right"
                    class="size-4 shrink-0 text-gray-400 transition-transform"
                    x-bind:class="{ 'rotate-90': narrowOpen }"
                />

                {{ __('filament-attachment-library::views.tree.heading') }}

                <span x-show="! narrowOpen" class="ms-auto flex min-w-0 items-center gap-1.5 text-primary-700 dark:text-primary-400">
                    <x-filament::icon icon="heroicon-o-folder-open" class="size-5 shrink-0"/>
                    <span class="truncate" x-text="currentName()"></span>
                </span>
            </button>

            <x-filament::icon-button
                icon="heroicon-o-folder-plus"
                color="gray"
                class="fal-tree-button"
                :label="__('filament-attachment-library::views.actions.directory.create')"
                :tooltip="__('filament-attachment-library::views.actions.directory.create')"
                wire:click="mountAction('createDirectory')"
            />
        </div>

        <div class="flex flex-col gap-2 pb-1.5 max-md:hidden md:pb-0" x-bind:class="{ 'max-md:hidden': ! narrowOpen }">
            <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                <x-filament::input
                    type="search"
                    x-model="query"
                    aria-label="{{ __('filament-attachment-library::views.tree.search') }}"
                    placeholder="{{ __('filament-attachment-library::views.tree.search') }}"
                />
            </x-filament::input.wrapper>

            {{-- One set of listeners for every row: the rows themselves stay as light as possible --}}
            <div
                x-ref="scroller"
                class="max-h-80 overflow-y-auto md:max-h-[calc(100vh-var(--fal-info-top,6rem)-9rem)]"
                x-bind:data-pending="pending !== null"
                x-on:click="onTreeClick($event)"
                x-on:keydown="onTreeKeydown($event)"
                x-on:dragover="onTreeDragOver($event)"
                x-on:dragleave="onTreeDragLeave($event)"
                x-on:drop="onTreeDrop($event)"
                x-on:scroll.passive="closeMenu()"
            >
                <ul x-ref="tree" x-show="! searching()" class="flex flex-col gap-0.5">
                    <li>
                        <div
                            class="fal-tree-row"
                            style="padding-inline-start: 0.25rem"
                            data-directory=""
                            x-bind:data-current="isCurrent('')"
                            x-bind:data-drop-target="dropPath === ''"
                        >
                            <button type="button" class="fal-tree-open" data-tree-action="open" x-bind:aria-current="isCurrent('')">
                                <x-filament::icon icon="heroicon-o-home"/>
                                <span class="fal-tree-name">{{ __('filament-attachment-library::views.home') }}</span>
                                <span class="fal-tree-count">{{ $tree['rootCount'] }}</span>
                            </button>
                        </div>
                    </li>

                    @foreach($tree['nodes'] as $node)
                        <x-filament-attachment-library::directory.tree-node :node="$node"/>
                    @endforeach
                </ul>

                <ul x-show="searching()" x-cloak class="flex flex-col gap-0.5">
                    <template x-for="directory in results()" x-bind:key="directory.path">
                        <li>
                            <div class="fal-tree-row fal-tree-row-result" x-bind:data-directory="directory.path">
                                <button type="button" class="fal-tree-open" data-tree-action="open">
                                    <x-filament::icon icon="heroicon-o-folder"/>
                                    <span class="min-w-0 flex-1">
                                        <span class="fal-tree-name block" x-text="directory.name"></span>
                                        <span class="fal-tree-parents" x-show="directory.parents" x-text="directory.parents"></span>
                                    </span>
                                </button>
                            </div>
                        </li>
                    </template>

                    <li x-show="results().length === 0" class="px-2 py-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('filament-attachment-library::views.tree.no_results') }}
                    </li>
                </ul>
            </div>
        </div>

        {{-- One menu for the whole tree, placed at the row it was opened from. It sits outside the
             scrolling list, which would cut it off in a tree of only a few rows. --}}
        <div
            x-ref="menu"
            x-show="menu"
            x-cloak
            role="menu"
            x-on:click.outside="onMenuOutside($event)"
            x-on:focusout="onMenuFocusOut($event)"
            {{-- Stopped here, or a browser modal around the tree would close on the same key --}}
            x-on:keydown.escape.stop="closeMenu(true)"
            x-bind:style="menu ? { top: menu.top + 'px' } : {}"
            class="absolute end-3 z-10 w-48 rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
        >
            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item icon="heroicon-o-folder-plus" role="menuitem" x-on:click.stop="createSubdirectory()">
                    {{ __('filament-attachment-library::views.actions.directory.create_sub') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-o-pencil-square" role="menuitem" x-on:click.stop="renameDirectory()">
                    {{ __('filament-attachment-library::views.actions.directory.rename') }}
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-o-trash" color="danger" role="menuitem" x-on:click.stop="deleteDirectory()">
                    {{ __('filament-attachment-library::views.actions.directory.delete') }}
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </div>
    </nav>
</div>
