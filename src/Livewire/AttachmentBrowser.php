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
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

/**
 * @property Form $uploadAttachmentForm
 * @property Form $createDirectoryForm
 */
class AttachmentBrowser extends Component implements HasActions, HasForms
{
    use InteractsWithActionsUsingAlpineJS;
    use InteractsWithForms;
    use WithPagination;

    #[Url(history: true, keep: true)]
    public ?string $currentPath = null;

    #[Url(history: true, keep: true)]
    public string $sortBy = 'name';

    #[Url(history: true, keep: true)]
    public int $pageSize = 25;

    public string $search = '';
    public string $mime = '';

    protected string $view = 'filament-attachment-library::livewire.attachment-browser';

    public ?array $createDirectoryFormState = [];

    public ?array $uploadFormState = ['attachment' => []];

    const SORTABLE_FIELDS = [
        'name',
        'created_at',
        'updated_at',
    ];

    const PAGE_SIZES = [5, 25, 50, 100];

    const FILTERABLE_FILE_TYPES = [
        'all' => '',
        'image' => 'image/*',
        'audio' => 'audio/*',
        'video' => 'video/*',
        'pdf' => 'application/pdf',
    ];

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
        return RenameDirectoryAction::make('renameDirectory')
            ->setCurrentPath($this->currentPath);
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
        return EditAttachmentAction::make('editAttributeAttachmentAction')
            ->setCurrentPath($this->currentPath);
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

    #[On('set-mime')]
    public function setMime(?string $mime): void
    {
        $this->mime = $mime;
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
        $this->currentPath = empty($this->currentPath) ? null : $this->currentPath;

        if ($this->currentPath !== null && !AttachmentManager::destinationExists($this->currentPath)) {
            $this->currentPath = null;
        }

        $attachments = Attachment::all();
        $attachments = $this->applyFiltering($attachments, true);
        $attachments = $this->applySorting($attachments);

        $directories = AttachmentManager::directories($this->currentPath);
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
     * Return filtered attachments or directories.
     */
    private function applyFiltering(Collection $items, bool $attachment = false): Collection
    {
        if ($this->search) {
            $items = $items->filter(fn ($item) => str_contains(strtolower($item->name), strtolower($this->search)));
        } else {
            $items = $items->filter(fn ($item) => $item->path === $this->currentPath);
        }

        if ($this->mime && $attachment) {
            $items = $items->filter(fn ($item) => fnmatch($this->mime, $item->mime_type));
        }

        return $items;
    }

    /**
     * Return sorted attachments or directories.
     */
    private function applySorting(Collection $items): Collection
    {
        $descending = str_starts_with($this->sortBy, '!');
        $sortKey = $descending ? ltrim($this->sortBy, '!') : $this->sortBy;

        // Return unsorted collection if sort key is not found.
        if (!in_array($sortKey, $this::SORTABLE_FIELDS)) {
            return $items;
        }

        return $descending
            ? $items->sortByDesc($sortKey)
            : $items->sortBy($sortKey);
    }
}
