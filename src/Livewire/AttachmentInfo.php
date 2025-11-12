<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use VanOns\FilamentAttachmentLibrary\Actions\DeleteAttachmentAction;
use VanOns\FilamentAttachmentLibrary\Actions\EditAttachmentAction;
use VanOns\FilamentAttachmentLibrary\Actions\OpenAttachmentAction;
use VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

#[Lazy]
class AttachmentInfo extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?AttachmentViewModel $attachment;

    public string $class = '';

    public ?string $currentPath = null;

    public bool $contained = true;

    #[On('highlight-attachment')]
    public function highlightAttachment(?int $id): void
    {
        $attachment = Attachment::find($id);

        if (!$attachment) {
            $this->attachment = null;
            return;
        }

        $this->attachment = new AttachmentViewModel($attachment);
    }

    #[On('dehighlight-attachment')]
    public function dehighlightAttachment(int $id): void
    {
        if (isset($this->attachment) && $this->attachment->id !== $id) {
            return;
        }

        $this->attachment = null;
    }

    public function deleteAttachmentAction(): Action
    {
        return DeleteAttachmentAction::make('deleteAttachment');
    }

    public function openAttachmentAction(): Action
    {
        return OpenAttachmentAction::make('openAttachment');
    }

    public function editAttachmentAction(): Action
    {
        return EditAttachmentAction::make('editAttributeAttachmentAction')->setCurrentPath($this->currentPath);
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
