<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class AttachmentInfoModal extends Component
{
    protected string $view = 'filament-attachment-library::livewire.attachment-info-modal';

    public function render()
    {
        return view($this->view);
    }
}
