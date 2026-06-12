<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use VanOns\FilamentAttachmentLibrary\Actions\Traits\HasCurrentPath;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\FilamentAttachmentLibrary\Rules\HasValidExtension;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class UploadAttachmentsAction extends Action
{
    use HasCurrentPath;

    protected function setUp(): void
    {
        $this->label(__('filament-attachment-library::views.actions.attachment.upload'));

        $this->icon('heroicon-o-arrow-up-tray');

        $this->modalHeading(__('filament-attachment-library::forms.upload_attachment.heading'));

        $validationMessages = Lang::get('validation');

        // Lazy: setUp() runs inside make(), before setCurrentPath() — the rules must not
        // capture $this->currentPath until the schema is actually resolved.
        $this->schema(fn () => [
            FileUpload::make('attachments')
                ->rules([
                    new AllowedFilename(),
                    new DestinationExists($this->currentPath),
                    new HasValidExtension(),
                    ...Config::get('filament-attachment-library.upload_rules', []),
                ])
                ->multiple()
                ->required()
                ->label(__('filament-attachment-library::forms.upload_attachment.name'))
                ->fetchFileInformation()
                ->saveUploadedFileUsing(
                    function (BaseFileUpload $component, TemporaryUploadedFile $file) {
                        $attachment = AttachmentManager::upload($file, $this->currentPath);

                        $livewire = $this->getLivewire();
                        if ($livewire instanceof AttachmentBrowser) {
                            $livewire->selectAttachment($attachment->id);
                        }

                        $component->removeUploadedFile($file);
                    }
                )->validationMessages([
                    ...(is_array($validationMessages) ? $validationMessages : []),
                    DestinationExists::class => __('filament-attachment-library::validation.destination_exists'),
                    AllowedFilename::class => __('filament-attachment-library::validation.allowed_filename'),
                    HasValidExtension::class => __('filament-attachment-library::validation.invalid_extension'),
                ]),
        ]);

        $this->modalSubmitActionLabel(__('filament-attachment-library::views.actions.attachment.upload'));

        $this->action(function () {
            Notification::make()
                ->title(__('filament-attachment-library::notifications.attachment.created'))
                ->success()
                ->send();
        });
    }
}
