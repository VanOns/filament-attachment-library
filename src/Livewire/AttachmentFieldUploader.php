<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use VanOns\FilamentAttachmentLibrary\Concerns\HandlesDroppedFiles;
use VanOns\FilamentAttachmentLibrary\Filament\Pages\AttachmentLibrary;

/**
 * Invisible companion component for the AttachmentField: receives files dropped onto the
 * field (the field itself lives in the consuming form's Livewire component, which has no
 * upload pipeline) and hands the uploaded attachment ids back to the field via an event.
 */
class AttachmentFieldUploader extends Component
{
    use HandlesDroppedFiles;
    use WithFileUploads;

    public string $statePath = '';

    protected function droppedFilesPath(): ?string
    {
        return AttachmentLibrary::getBasePath();
    }

    protected function finishDroppedUploads(array $attachmentIds): void
    {
        $this->dispatch('attachments-uploaded-' . md5($this->statePath), ids: $attachmentIds);
    }

    public function render(): View
    {
        return view('filament-attachment-library::livewire.attachment-field-uploader');
    }
}
