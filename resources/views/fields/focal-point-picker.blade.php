<x-dynamic-component
        :component="$getFieldWrapperView()"
        :field="$field"
>
    <div x-data="attachmentFocalPicker({ state: $wire.$entangle('{{ $getStatePath() }}') })" class="flex flex-col gap-3">
        <div class="relative overflow-hidden rounded-lg">
            <img
                class="block w-full cursor-crosshair"
                draggable="false"
                x-on:click="setPosition($event)"
                x-on:dragover="$event.preventDefault()"
                x-on:drop="setPosition($event)"
                src="{{ $getImage() }}"
                alt=""
            />
            <div
                draggable="true"
                class="pointer-events-none absolute flex size-7 items-center justify-center rounded-full border-2 border-white bg-primary-500/40 shadow-md ring-1 ring-black/30"
                x-bind:style="{ left: state?.x + '%', top: state?.y + '%', transform: 'translate(-50%, -50%)' }"
            >
                <div class="size-1.5 rounded-full bg-white"></div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-filament::input.wrapper class="flex-1" prefix="X" suffix="%" :disabled="true">
                <x-filament::input type="text" x-bind:value="state?.x" :disabled="true" />
            </x-filament::input.wrapper>
            <x-filament::input.wrapper class="flex-1" prefix="Y" suffix="%" :disabled="true">
                <x-filament::input type="text" x-bind:value="state?.y" :disabled="true" />
            </x-filament::input.wrapper>
        </div>

        <x-filament::link tag="button" type="button" size="sm" x-on:click="reset()" class="self-start">
            {{ __('filament-attachment-library::forms.focal_point.reset') }}
        </x-filament::link>

        <div class="border-t border-gray-100 pt-3 dark:border-white/10">
            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">{{ __('filament-attachment-library::forms.focal_point.crop_preview') }}</p>
            <div class="flex items-end gap-3">
                @foreach(['1:1' => 'size-12', '4:5' => 'h-12 w-[38px]', '16:9' => 'h-12 w-[85px]', '9:16' => 'h-12 w-[27px]'] as $ratio => $size)
                    <div class="flex flex-col items-center gap-1">
                        <div class="{{ $size }} overflow-hidden rounded-md ring-1 ring-gray-950/10 dark:ring-white/10">
                            <img src="{{ $getImage() }}" alt="" class="size-full object-cover" x-bind:style="{ objectPosition: objectPosition() }">
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $ratio }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-dynamic-component>
