@props(['node', 'depth' => 1])

@php
    /**
     * @var array{name: string, path: string, count: int, children: array<int, array<string, mixed>>} $node
     * @var int $depth
     */
    $hasChildren = $node['children'] !== [];
@endphp

<li wire:key="directory-tree-node-{{ md5($node['path']) }}">
    <div
        class="fal-tree-row"
        style="padding-inline-start: {{ $depth - 0.75 }}rem"
        data-directory="{{ $node['path'] }}"
        x-bind:data-current="isCurrent($el.dataset.directory)"
        x-bind:data-drop-target="dropPath === $el.dataset.directory"
        @if($hasChildren)
            data-has-children
            x-bind:data-expanded="isExpanded($el.dataset.directory)"
        @endif
    >
        @if($hasChildren)
            <button
                type="button"
                class="fal-tree-toggle"
                data-tree-action="toggle"
                aria-label="{{ __('filament-attachment-library::views.tree.toggle', ['directory' => $node['name']]) }}"
                x-bind:aria-expanded="isExpanded($el.parentElement.dataset.directory)"
            >
                <x-filament::icon icon="heroicon-m-chevron-right"/>
            </button>
        @else
            <span class="fal-tree-toggle" aria-hidden="true"></span>
        @endif

        <button
            type="button"
            class="fal-tree-open"
            data-tree-action="open"
            x-bind:aria-current="isCurrent($el.parentElement.dataset.directory)"
        >
            <x-filament::icon icon="heroicon-o-folder"/>
            <span class="fal-tree-name" title="{{ $node['name'] }}">{{ $node['name'] }}</span>
            <span class="fal-tree-count">{{ $node['count'] }}</span>
        </button>

        <button
            type="button"
            class="fal-tree-menu"
            data-tree-action="menu"
            aria-haspopup="menu"
            aria-label="{{ __('filament-attachment-library::views.tree.options', ['directory' => $node['name']]) }}"
            x-bind:aria-expanded="menu?.path === $el.parentElement.dataset.directory"
        >
            <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="size-5"/>
        </button>
    </div>

    @if($hasChildren)
        <ul x-show="isExpanded($el.previousElementSibling.dataset.directory)" x-cloak>
            @foreach($node['children'] as $child)
                <x-filament-attachment-library::directory.tree-node :node="$child" :depth="$depth + 1"/>
            @endforeach
        </ul>
    @endif
</li>
