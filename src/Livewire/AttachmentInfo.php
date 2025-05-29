<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

#[Lazy]
class AttachmentInfo extends Component
{
    public ?Attachment $attachment;

    public string $class = '';

    #[On('highlight-attachment')]
    public function highlightAttachment(?int $id): void
    {
        /** @var Attachment $attachment */
        $attachment = Attachment::find($id);

        $this->attachment = $attachment;
    }

    #[On('dehighlight-attachment')]
    public function dehighlightAttachment(int $id): void
    {
        if (isset($this->attachment) && $this->attachment->id !== $id) {
            return;
        }

        $this->attachment = null;
    }

    public function mount()
    {
        $this->attachment = null;
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="p-6 flex-1 sticky top-24 w-full min-w-[400px] flex-grow-0 self-start rounded-l-xl bg-white dark:bg-gray-900 rounded-lg hidden md:block max-w-md">
            <x-filament::loading-indicator class="h-8 w-8 mx-auto"/>
        </div>
        HTML;
    }

    public function render(): View
    {
        return view('filament-attachment-library::livewire.attachment-info');
    }
}
