<div class="min-w-full md:min-w-max">
    <nav class="fi-breadcrumbs">
        <ol class="fi-breadcrumbs-list flex flex-wrap items-center gap-x-2">

            {{-- Home breadcrumb --}}
            <li class="fi-breadcrumbs-item flex gap-x-2">
                <a href="#" wire:click="openPath(null)" class="text-sm font-medium text-gray-500 transition duration-75 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    {{ __('filament-attachment-library::views.home')  }}
                </a>
            </li>

            {{-- Full path in breadcrumbs --}}
            @foreach($this->breadcrumbs as $key => $breadcrumb)
                <li class="fi-breadcrumbs-item flex gap-x-2">
                    <x-filament::icon icon="heroicon-o-chevron-left" class="h-4 w-4 m-auto text-gray-400 dark:text-gray-500"/>

                    <a href="#" wire:click="openPath('{{ $key }}')" class="fi-breadcrumbs-item-label text-sm font-medium text-gray-500 transition duration-75 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ $breadcrumb }}
                    </a>

                </li>
            @endforeach

        </ol>
    </nav>
</div>
