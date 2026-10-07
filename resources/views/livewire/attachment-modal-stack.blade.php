<div>
    @for($level = 0; $level < $levels; $level++)
        <x-filament-attachment-library::attachment-browser-modal
            :$basePath
            :$level
            :id="\VanOns\FilamentAttachmentLibrary\Livewire\AttachmentModalStack::modalId($level)"
            wire:key="attachment-browser-modal-{{ $level }}"
        />
    @endfor
</div>
