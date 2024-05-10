<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

#[Lazy]
class AttachmentInfo extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?Attachment $attachment;

    protected string $view = 'filament-attachment-library::livewire.attachment-info';

    public function openAttachmentAction(): Action
    {
        return Action::make('openAttachment')
            ->color('gray')
            ->url($this->attachment->url ?? '')->openUrlInNewTab();
    }

    public function renameAttachmentAction(): Action
    {
        return Action::make('renameAttachment')
            ->color('gray')
            ->form([TextInput::make('name')->rule(new DestinationExists(null))])
            ->mountUsing(fn (ComponentContainer $form) => $form->fill(['name' => $this->attachment->name]))
            ->action(fn (array $data) => AttachmentManager::rename($this->attachment, $data['name']));
    }

    public function deleteAttachmentAction(): Action
    {
        return Action::make('deleteAttachment')
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn() => $this->attachment->delete());
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="p-6 flex-1 sticky top-24 w-full min-w-[400px] flex-grow-0 self-start rounded-l-xl bg-white dark:bg-gray-900 rounded-lg hidden md:block max-w-md">
            <x-filament::loading-indicator class="h-8 w-8 mx-auto"/>
        </div>
        HTML;
    }

    public function render()
    {
        return view($this->view);
    }
}
