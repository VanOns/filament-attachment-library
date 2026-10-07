@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
    $posterUrl = $attachment->posterUrl();
@endphp

<div class="overflow-hidden rounded-xl bg-gray-950 ring-1 ring-gray-950/5 dark:ring-white/10">
    <video
        src="{{ $attachment->url }}"
        @if($posterUrl) poster="{{ $posterUrl }}" @endif
        controls
        preload="metadata"
        class="mx-auto block max-h-[420px] w-full object-contain"
    >
        @foreach($attachment->attachment->captions as $caption)
            <track
                kind="captions"
                src="{{ $caption->url }}"
                srclang="{{ $caption->pivot->language }}"
                label="{{ $caption->pivot->label ?: $caption->pivot->language }}"
                @if($caption->pivot->is_default) default @endif
            >
        @endforeach
    </video>
</div>
