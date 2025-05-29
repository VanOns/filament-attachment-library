<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class AttachmentInfoModal extends Component
{
    public function render()
    {
        return view('filament-attachment-library::livewire.attachment-info-modal');
    }
}
