@props(['attachment'])

@php
    /**
     * @var \VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel $attachment
     */
    $posterUrl = $attachment->posterUrl();
@endphp

<video
    src="{{ $attachment->url }}"
    @if($posterUrl) poster="{{ $posterUrl }}" @endif
    controls
    {{ $attributes }}
>
    @foreach($attachment->attachment->captions as $caption)
        <track
            kind="captions"
            src="{{ $caption->url }}"
            srclang="{{ $caption->pivot->language }}"
            label="{{ $attachment::captionLabel($caption) }}"
            @if($caption->pivot->is_default) default @endif
        >
    @endforeach
</video>
