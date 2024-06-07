<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithPagination;
use VanOns\FilamentAttachmentLibrary\Actions\DeleteAttachmentAction;
use VanOns\FilamentAttachmentLibrary\Actions\DeleteDirectoryAction;
use VanOns\FilamentAttachmentLibrary\Actions\EditAttachmentAction;
use VanOns\FilamentAttachmentLibrary\Actions\OpenAttachmentAction;
use VanOns\FilamentAttachmentLibrary\Actions\RenameDirectoryAction;
use VanOns\FilamentAttachmentLibrary\Concerns\InteractsWithActionsUsingAlpineJS;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;

class AttachmentBrowser extends Component implements HasActions, HasForms
{
    use InteractsWithActionsUsingAlpineJS;
    use InteractsWithForms;
    use WithPagination;

    #[Url(history: true, keep: true)]
    public ?string $currentPath = null;

    #[Url(history: true, keep: true)]
    public string $sortBy = 'name_ascending';

    #[Url(history: true, keep: true)]
    public int $pageSize = 25;

    public string $search = '';

    protected string $view = 'filament-attachment-library::livewire.attachment-browser';

    public ?array $createDirectoryFormState = [];

    public ?array $uploadFormState = ['attachment' => []];

    public function render()
    {
        return view($this->view);
    }

    public function deleteDirectoryAction(): Action
    {
        return DeleteDirectoryAction::make('renameDirectory');
    }

    public function renameDirectoryAction(): Action
    {
        return RenameDirectoryAction::make('renameDirectory')->setCurrentPath($this->currentPath);
    }

    public function deleteAttachmentAction(): Action
    {
        return DeleteAttachmentAction::make('deleteAttachment');
    }

    public function openAttachmentAction(): Action
    {
        return OpenAttachmentAction::make('openAttachment');
    }

    public function editAttributeAttachmentAction(): Action
    {
        return EditAttachmentAction::make('editAttributeAttachmentAction');
    }

    protected function getForms(): array
    {
        return [
            'uploadAttachmentForm',
            'createDirectoryForm',
        ];
    }

    /**
     * Form schema for UploadAttachmentForm.
     */
    public function uploadAttachmentForm(Form $form): Form
    {
        return $form->schema([
            FileUpload::make('attachment')
                ->rules([new AllowedFilename(), new DestinationExists($this->currentPath)])
                ->multiple()
                ->required()
                ->label(__('filament-attachment-library::forms.upload-attachment.name'))
                ->fetchFileInformation()
                ->saveUploadedFileUsing(
                    function (BaseFileUpload $component, TemporaryUploadedFile $file) {
                        $attachment = AttachmentManager::upload($file, $this->currentPath);
                        $this->dispatch('highlight-attachment', $attachment->id);
                        $component->removeUploadedFile($file);
                    }
                )->validationMessages([
                    DestinationExists::class => __('filament-attachment-library::validation.destination_exists'),
                    AllowedFilename::class => __('filament-attachment-library::validation.allowed_filename'),
                ]),
        ])->statePath('uploadFormState');
    }

    /**
     * Form schema for CreateDirectoryForm.
     */
    public function createDirectoryForm(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->rules([
                    new DestinationExists($this->currentPath),
                    new AllowedFilename(),
                ])->required()
                ->label(__('filament-attachment-library::forms.create-directory.name')),
        ])->statePath('createDirectoryFormState');
    }

    /**
     * Submit handler for UploadAttachmentForm.
     */
    public function saveUploadAttachmentForm()
    {
        $this->uploadAttachmentForm->getState();

        Notification::make()
            ->title(__('filament-attachment-library::notifications.attachment.created'))
            ->success()
            ->send();
        $this->dispatch('hide-form', form: 'uploadAttachment');
    }

    /**
     * Submit handler for CreateDirectoryForm.
     */
    public function saveCreateDirectoryForm()
    {
        $state = $this->createDirectoryForm->getState();
        $path = implode('/', (array_filter([$this->currentPath, $state['name']])));

        AttachmentManager::createDirectory($path);

        $this->createDirectoryForm->fill();

        Notification::make()
            ->title(__('filament-attachment-library::notifications.directory.created'))
            ->success()
            ->send();
        $this->dispatch('hide-form', form: 'createDirectory');
    }

    /**
     * Set current path.
     */
    #[On('open-path')]
    public function openPath(?string $path): void
    {
        $this->currentPath = $path;
        $this->dispatch('highlight-attachment', null);
    }

    /**
     * Reset page on search query update.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Return current path in parts (breadcrumbs).
     */
    #[Computed]
    public function breadcrumbs(): array
    {
        $crumbs = array_filter(explode('/', $this->currentPath));
        $breadcrumbs = [];

        foreach ($crumbs as $index => $crumb) {
            $pathToCrumb = implode('/', array_slice($crumbs, 0, $index + 1));
            $breadcrumbs[$pathToCrumb] = $crumb;
        }

        return $breadcrumbs;
    }

    /**
     * Return sorted and filtered attachments as paginator.
     */
    #[Computed]
    protected function paginator(): LengthAwarePaginator
    {
        $path = empty($this->currentPath) ? null : $this->currentPath;

        if ($path !== null && ! AttachmentManager::destinationExists($this->currentPath)) {
            $this->currentPath = null;
            $path = null;
        }

        $attachments = AttachmentManager::files($path);
        $attachments = $this->applyFiltering($attachments);
        $attachments = $this->applySorting($attachments);

        $directories = AttachmentManager::directories($path);
        $directories = $this->applyFiltering($directories);
        $directories = $this->applySorting($directories);

        $items = collect($directories)->merge($attachments);

        $pageItems = $items->skip($this->pageSize * ($this->getPage() - 1))
            ->take($this->pageSize);

        return new LengthAwarePaginator(
            $pageItems,
            count($items),
            $this->pageSize,
            $this->getPage()
        );
    }

    /**
     * Return filtered attachments.
     */
    private function applyFiltering(Collection $items): Collection
    {
        if ($this->search) {
            $items = $items->filter(fn ($item) => str_contains(strtolower($item->name), strtolower($this->search)));
        }

        return $items;
    }

    /**
     * Return sorted attachments.
     */
    private function applySorting(Collection $items): Collection
    {
        return match ($this->sortBy) {
            'created_at_ascending' => $items->sortBy('created_at'),
            'created_at_descending' => $items->sortByDesc('created_at'),
            'name_descending' => $items->sortByDesc('name'),
            default => $items->sortBy('name')
        };
    }
}
