<?php

namespace VanOns\FilamentAttachmentLibrary\Concerns;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\FilamentAttachmentLibrary\Rules\HasValidExtension;
use VanOns\LaravelAttachmentLibrary\Exceptions\DestinationAlreadyExistsException;
use VanOns\LaravelAttachmentLibrary\Exceptions\DisallowedCharacterException;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

trait HandlesDroppedFiles
{
    /** @var array<int, TemporaryUploadedFile> */
    public array $droppedFiles = [];

    /**
     * Directory the dropped files are uploaded to.
     */
    abstract protected function droppedFilesPath(): ?string;

    /**
     * Called for every successfully uploaded drop.
     */
    protected function handleUploadedDrop(Attachment $attachment): void
    {
    }

    /**
     * Called once after the drop has been processed, with the uploaded attachment ids.
     */
    protected function finishDroppedUploads(array $attachmentIds): void
    {
    }

    protected function dropsDisabled(): bool
    {
        return false;
    }

    /**
     * Process files dropped onto the component. Runs inside Livewire's _finishUpload request,
     * so the component re-renders with the new attachments in the same round-trip.
     */
    public function updatedDroppedFiles(): void
    {
        $files = $this->droppedFiles;
        // Clear first so a failure can never leave stale temp-file refs in the snapshot.
        $this->droppedFiles = [];

        if ($this->dropsDisabled() || $files === []) {
            return;
        }

        $uploadedIds = [];

        foreach ($files as $file) {
            if (!$file instanceof TemporaryUploadedFile) {
                continue;
            }

            try {
                $validator = Validator::make(['file' => $file], ['file' => [
                    'file',
                    new AllowedFilename(),
                    new DestinationExists($this->droppedFilesPath()),
                    new HasValidExtension(),
                    ...Config::get('filament-attachment-library.upload_rules', []),
                ]]);

                if ($validator->fails()) {
                    $this->notifyDropFailure($file->getClientOriginalName(), $validator->errors()->first('file'));
                    continue;
                }

                $attachment = AttachmentManager::upload($file, $this->droppedFilesPath());
                $this->handleUploadedDrop($attachment);
                $uploadedIds[] = $attachment->id;
            } catch (DestinationAlreadyExistsException) {
                $this->notifyDropFailure($file->getClientOriginalName(), __('filament-attachment-library::validation.destination_exists'));
            } catch (DisallowedCharacterException) {
                $this->notifyDropFailure($file->getClientOriginalName(), __('filament-attachment-library::validation.allowed_filename'));
            } catch (\Throwable $e) {
                // Must swallow: an uncaught throw 500s _finishUpload and wedges the client-side
                // upload queue for this property until the page is reloaded.
                report($e);
                $this->notifyDropFailure($file->getClientOriginalName(), __('filament-attachment-library::notifications.attachment.upload_failed'));
            } finally {
                // AttachmentManager::upload() copies the contents but does not consume the temp file.
                rescue(fn () => $file->delete(), report: false);
            }
        }

        if ($uploadedIds !== []) {
            $this->finishDroppedUploads($uploadedIds);

            Notification::make()
                ->title(__('filament-attachment-library::notifications.attachment.created'))
                ->success()
                ->send();
        }
    }

    protected function notifyDropFailure(string $filename, string $message): void
    {
        Notification::make()
            ->title($filename)
            ->body($message)
            ->danger()
            ->send();
    }
}
