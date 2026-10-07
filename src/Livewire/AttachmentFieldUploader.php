<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use VanOns\FilamentAttachmentLibrary\Concerns\HandlesDroppedFiles;
use VanOns\FilamentAttachmentLibrary\Filament\Pages\AttachmentLibrary;
use VanOns\FilamentAttachmentLibrary\Rules\MatchesFileFilter;

/**
 * Invisible companion component for the AttachmentField: receives files dropped onto the
 * field (the field itself lives in the consuming form's Livewire component, which has no
 * upload pipeline) and hands the uploaded attachment ids back to the field via an event.
 */
class AttachmentFieldUploader extends Component
{
    use HandlesDroppedFiles;
    use WithFileUploads;

    /**
     * Locked: a tampered statePath would route uploads to another field's event.
     */
    #[Locked]
    public string $statePath = '';

    /**
     * Locked: nulling the mime client-side would bypass the server-side type check.
     */
    #[Locked]
    public ?string $mime = null;

    /**
     * Locked: for the same reason as the mime.
     *
     * @var array<int, string>
     */
    #[Locked]
    public array $extensions = [];

    protected function droppedFilesPath(): ?string
    {
        return AttachmentLibrary::getBasePath();
    }

    protected function droppedFileRules(): array
    {
        return [new MatchesFileFilter($this->mime, $this->extensions)];
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
