<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use VanOns\FilamentAttachmentLibrary\Actions\Traits\HasCurrentPath;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\LaravelAttachmentLibrary\Exceptions\DestinationAlreadyExistsException;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class ReplaceAttachmentAction extends Action
{
    use HasCurrentPath;

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.attachment.replace'));

        $this->color('gray');

        $validationMessages = Lang::get('validation');

        $this->schema(fn (array $arguments) => [
            FileUpload::make('file')
                ->rules([
                    new AllowedFilename(),
                    new DestinationExists($this->currentPath),
                    ...Config::get('filament-attachment-library.upload_rules', []),
                ])
                ->required()
                ->label(__('filament-attachment-library::forms.upload_attachment.name'))
                ->helperText(__('filament-attachment-library::forms.replace_attachment.helper_text'))
                ->fetchFileInformation()
                ->saveUploadedFileUsing(
                    function (BaseFileUpload $component, TemporaryUploadedFile $file) use ($arguments) {
                        /** @var Attachment $attachment */
                        $attachment = Attachment::find($arguments['attachment_id']);

                        try {
                            AttachmentManager::replace($file, $attachment);

                            $this->getLivewire()->dispatch('refresh-attachments');

                            Notification::make()
                                ->title(__('filament-attachment-library::notifications.attachment.replaced'))
                                ->success()
                                ->send();
                        } catch (DestinationAlreadyExistsException $e) {
                            Notification::make()
                                ->title(__('filament-attachment-library::validation.destination_exists'))
                                ->danger()
                                ->send();
                        }

                        $component->removeUploadedFile($file);
                    }
                )->validationMessages([
                    ...(is_array($validationMessages) ? $validationMessages : []),
                    DestinationExists::class => __('filament-attachment-library::validation.destination_exists'),
                    AllowedFilename::class => __('filament-attachment-library::validation.allowed_filename'),
                ]),
        ]);

        $this->modalSubmitActionLabel(__('filament-attachment-library::views.actions.attachment.replace'));
    }

    protected function getDirectories(?string $path = null, bool $recursive = false): Collection
    {
        $directories = AttachmentManager::directories($path)->pluck('fullPath');

        if ($recursive) {
            foreach ($directories as $directory) {
                $directories = $directories->merge($this->getDirectories($directory, recursive: true));
            }
        }

        return $directories;
    }
}
