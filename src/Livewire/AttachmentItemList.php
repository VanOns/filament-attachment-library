<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class AttachmentItemList extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    use WithPagination;

    public Collection|Attachment $attachments;

    public ?string $statePath;

    protected string $view = 'filament-attachment-library::livewire.attachment-item-list';

    public function render()
    {
        return view($this->view);
    }
}
