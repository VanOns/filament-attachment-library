<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use VanOns\FilamentAttachmentLibrary\Enums\Layout;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

#[Lazy]
class AttachmentItemList extends Component
{
    #[Reactive]
    public Collection|Attachment $attachments;

    #[Reactive]
    public ?string $currentPath;

    #[Reactive]
    public Layout $layout = Layout::GRID;

    public ?string $statePath;

    public bool $withContextMenu = true;

    public bool $disabled = false;

    public bool $inModal = false;

    public string $class = '';

    public function placeholder()
    {
        return <<<'HTML'
        <div class="flex-1">
            <x-filament::loading-indicator class="h-8 w-8 mx-auto"/>
        </div>
        HTML;
    }

    public function render(): View
    {
        return view('filament-attachment-library::livewire.attachment-item-list');
    }
}
