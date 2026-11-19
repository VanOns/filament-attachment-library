{{-- Same layout and sizes as the attachment details, so the info panel keeps its height when they arrive --}}
<div {{ $attributes->class(['fal-info-skeleton']) }} aria-hidden="true">
    <div class="h-48 rounded-lg fal-skeleton"></div>

    <div class="mt-6">
        <div class="flex h-7 items-center"><div class="h-5 w-2/3 rounded fal-skeleton"></div></div>
        <div class="flex h-5 items-center"><div class="h-3.5 w-1/4 rounded fal-skeleton"></div></div>

        <hr class="mt-3 border-gray-200 dark:border-white/10">

        <div class="grid mt-3 gap-y-2 md:grid-cols-2">
            @foreach(['w-1/3', 'w-1/2', 'w-1/2', 'w-3/4'] as $width)
                <div class="flex h-6 items-center"><div @class(['h-4 rounded fal-skeleton', $width])></div></div>
            @endforeach

            <div class="col-span-2 rounded-lg px-3 py-2 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                <div class="flex h-5 items-center"><div class="h-3.5 w-full rounded fal-skeleton"></div></div>
                <div class="flex h-5 items-center"><div class="h-3.5 w-1/2 rounded fal-skeleton"></div></div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="grid grid-cols-2 gap-2 mt-4">
            @for($i = 0; $i < 4; $i++)
                <div class="h-9 rounded-lg fal-skeleton"></div>
            @endfor

            <div class="col-span-2 h-9 rounded-lg fal-skeleton"></div>
        </div>

        <div class="border-t border-gray-200 dark:border-white/10 pt-3 mt-4 -mb-2">
            <div class="flex h-4 items-center"><div class="h-3 w-1/4 rounded fal-skeleton"></div></div>
        </div>
    </div>
</div>
