<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Illuminate\Support\Collection;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

#[Lazy]
class AttachmentItemList extends Component
{
    #[Reactive]
    public Collection|Attachment $attachments;

    #[Reactive]
    public ?string $currentPath;

    public ?string $statePath;

    public bool $withContextMenu = true;

    protected string $view = 'filament-attachment-library::livewire.attachment-item-list';

    public function placeholder()
    {
        return <<<'HTML'
        <div class="flex-1">
            <x-filament::loading-indicator class="h-8 w-8 mx-auto"/>
        </div>
        HTML;
    }

    public function render()
    {
        return view($this->view);
    }
}
