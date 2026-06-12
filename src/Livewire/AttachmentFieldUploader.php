<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
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

    /** Locked: a tampered statePath would route uploads to another field's event. */
    #[Locked]
    public string $statePath = '';

    /** Locked: nulling the mime client-side would bypass the server-side type check. */
    #[Locked]
    public ?string $mime = null;

    protected function droppedFilesPath(): ?string
    {
        return AttachmentLibrary::getBasePath();
    }

    /**
     * Enforce the field's mime constraint on the server-detected mime type — the
     * client-side check works off the browser-supplied type and is bypassable.
     */
    protected function droppedFileRules(): array
    {
        if (!$this->mime) {
            return [];
        }

        return [function (string $attribute, mixed $value, Closure $fail) {
            if (!Str::is($this->mime, (string) $value->getMimeType())) {
                $fail(__('filament-attachment-library::notifications.attachment.upload_failed_wrong_type'));
            }
        }];
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
