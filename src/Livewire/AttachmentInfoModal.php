<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class AttachmentInfoModal extends Component
{
    public function render(): View
    {
        return view('filament-attachment-library::livewire.attachment-info-modal');
    }
}
