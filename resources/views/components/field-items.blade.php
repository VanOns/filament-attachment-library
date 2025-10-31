@props([ 'attachments', 'statePath'])

@php
    use VanOns\LaravelAttachmentLibrary\Facades\Glide;
    use VanOns\LaravelAttachmentLibrary\Facades\Resizer;
    /**
     * @var \Illuminate\Support\Collection<\VanOns\LaravelAttachmentLibrary\Models\Attachment> $attachments
     */
@endphp

<div>
    @if($attachments->isEmpty())
        <p class="inline-block border-2 border-dashed border-gray-300 dark:border-gray-600 p-4 rounded-xl font-medium text-gray-900 dark:text-gray-100">{{ __('filament-attachment-library::forms.attachment_field.no_file_selected') }}</p>
    @else
        <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,auto))] gap-4">
            @foreach($attachments as $attachment)
                <div class="max-w-80 aspect-square flex justify-center items-center relative rounded-xl overflow-hidden group bg-black/5 dark:bg-white/5 border shadow-xs border-black/10 dark:border-white/10">
                    @if($attachment->isImage())
                        @php
                            $thumbnailUrl = match(Glide::imageIsSupported($attachment->full_path)) {
                                true => Resizer::src($attachment)->height(320)->resize()['url'] ?? null,
                                default => $attachment->url,
                            };
                        @endphp

                        <img
                            alt="{{ $attachment->alt }}"
                            loading="lazy"
                            src="{{ $thumbnailUrl }}"
                            class="object-contain h-full w-full"
                        >
                    @endif

                    @if($attachment->isVideo())
                        <x-filament::icon icon="heroicon-o-film" class="size-24 stroke-gray-500" />
                    @endif

                    @if(!$attachment->isImage() && !$attachment->isVideo())
                        <x-filament::icon icon="heroicon-o-document" class="size-24 stroke-gray-500" />
                    @endif

                    <div @class([
                        'absolute inset-0 transition flex items-end group-hover:bg-black/50',
                        'opacity-0 group-hover:opacity-100' => $attachment->isImage(),
                        'bg-black/25' => !$attachment->isImage(),
                    ])>
                        <div class="text-white p-4">
                            <p class="font-bold">{{ \Illuminate\Support\Str::limit($attachment->filename, 100) }}</p>
                            <p>{{ round($attachment->size / 1024 / 1024, 2) }} MB</p>
                        </div>
                        <button class="opacity-0 group-hover:opacity-100 transition absolute top-4 right-4" x-on:click="$dispatch('attachment-removed', { id: {{ json_encode($attachment->id) }} })" type="button">
                            <x-filament::icon icon="heroicon-o-x-circle" class="size-8 text-white"/>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
