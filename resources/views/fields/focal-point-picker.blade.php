<x-dynamic-component
        :component="$getFieldWrapperView()"
        :field="$field"
>
    <div x-data="{
        state: $wire.$entangle('{{ $getStatePath() }}'),
        setPosition(event) {
            const x = event.offsetX
            const y = event.offsetY
            const width = event.target.width
            const height = event.target.height

            this.state = {
                x: Math.round((x / width) * 100),
                y: Math.round((y / height) * 100),
            }
        }
    }">
        <div class="relative">
            <img
                draggable="false"
                x-on:click="setPosition($event)"
                x-on:dragover="$event.preventDefault()"
                x-on:drop="setPosition($event)"
                src="{{ $getImage() }}"
            />
            <div
                draggable="true"
                class="absolute size-8 rounded-full bg-gray-500/50 flex shadow justify-center items-center"
                x-bind:style="{ left: state?.x + '%', top: state?.y + '%', transform: 'translate(-50%, -50%)' }"
            >
                <div class="size-4 rounded-full bg-white border-gray"></div>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-4 justify-center">
            <x-filament::input.wrapper class="w-32" prefix="X" suffix="%" :disabled="true">
                <x-filament::input type="text" x-bind:value="state?.x" :disabled="true" />
            </x-filament::input.wrapper>
            <x-filament::input.wrapper class="w-32" prefix="Y" suffix="%" :disabled="true">
                <x-filament::input type="text" x-bind:value="state?.y" :disabled="true" />
            </x-filament::input.wrapper>
        </div>
    </div>
</x-dynamic-component>
