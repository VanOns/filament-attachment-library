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
                    <svg  class="fi-breadcrumbs-item-separator flex h-5 w-5 text-gray-400 dark:text-gray-500 rtl:hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="#" wire:click="openPath('{{ $key }}')" class="fi-breadcrumbs-item-label text-sm font-medium text-gray-500 transition duration-75 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ $breadcrumb }}
                    </a>
                </li>
            @endforeach

        </ol>
    </nav>
</div>
