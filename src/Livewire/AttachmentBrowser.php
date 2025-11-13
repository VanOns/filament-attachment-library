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
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
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
use VanOns\FilamentAttachmentLibrary\Enums\Layout;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel;
use VanOns\FilamentAttachmentLibrary\ViewModels\DirectoryViewModel;
use VanOns\LaravelAttachmentLibrary\DataTransferObjects\Directory;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

/**
 * @property \Filament\Forms\Form $uploadAttachmentForm
 * @property \Filament\Forms\Form $createDirectoryForm
 */
class AttachmentBrowser extends Component implements HasActions, HasForms
{
    use InteractsWithActionsUsingAlpineJS;
    use InteractsWithForms;
    use WithPagination;

    #[Url(history: true, keep: true)]
    public ?string $currentPath = null;

    #[Url(history: true, keep: true)]
    public string $sortBy = 'name_asc';

    #[Url(history: true, keep: true)]
    public int $pageSize = 25;

    #[Url(history: true, keep: true)]
    public Layout $layout = Layout::GRID;

    public string $search = '';

    public ?string $mime = null;

    public bool $disableMimeFilter = false;

    public bool $multiple = false;

    public array $selected = [];

    public bool $disabled = false;

    public ?string $statePath = null;

    public ?array $createDirectoryFormState = [];

    public ?array $uploadFormState = ['attachment' => []];

    public const SORTABLE_FIELDS = [
        'name',
        'created_at',
        'updated_at',
    ];

    public const PAGE_SIZES = [5, 10, 25, 50];

    public const FILTERABLE_FILE_TYPES = [
        'all' => '',
        'image' => 'image/*',
        'audio' => 'audio/*',
        'video' => 'video/*',
        'pdf' => 'application/pdf',
    ];

    public function render(): View
    {
        $attachments = $this->getAttachments();
        $directories = $this->getDirectories();

        return view('filament-attachment-library::livewire.attachment-browser', compact('attachments', 'directories'));
    }

    public function mount(): void
    {
        if (!in_array($this->pageSize, self::PAGE_SIZES)) {
            $this->pageSize = 1;
        }

        if (!in_array($this->layout, Layout::cases())) {
            $this->layout = Layout::GRID;
        }
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
        $validationMessages = Lang::get('validation');

        return $form->components([
            FileUpload::make('attachment')
                ->rules([
                    new AllowedFilename(),
                    new DestinationExists($this->currentPath),
                    ...Config::get('filament-attachment-library.upload_rules', []),
                ])
                ->multiple()
                ->required()
                ->label(__('filament-attachment-library::forms.upload_attachment.name'))
                ->fetchFileInformation()
                ->saveUploadedFileUsing(
                    function (BaseFileUpload $component, TemporaryUploadedFile $file) {
                        $attachment = AttachmentManager::upload($file, $this->currentPath);
                        $this->selectAttachment($attachment->id);
                        $component->removeUploadedFile($file);
                    }
                )->validationMessages([
                    ...(is_array($validationMessages) ? $validationMessages : []),
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
        return $form->components([
            TextInput::make('name')
                ->rules([
                    new DestinationExists($this->currentPath),
                    new AllowedFilename(),
                ])->required()
                ->autocomplete(false)
                ->label(__('filament-attachment-library::forms.create_directory.name')),
        ])->statePath('createDirectoryFormState');
    }

    /**
     * Submit handler for UploadAttachmentForm.
     */
    public function saveUploadAttachmentForm(): void
    {
        $this->uploadAttachmentForm->getState();

        Notification::make()
            ->title(__('filament-attachment-library::notifications.attachment.created'))
            ->success()
            ->send();
    }

    /**
     * Submit handler for CreateDirectoryForm.
     */
    public function saveCreateDirectoryForm(): void
    {
        $state = $this->createDirectoryForm->getState();
        $path = implode('/', (array_filter([$this->currentPath, $state['name']])));

        AttachmentManager::createDirectory($path);

        $this->createDirectoryForm->fill();

        Notification::make()
            ->title(__('filament-attachment-library::notifications.directory.created'))
            ->success()
            ->send();
    }

    public function selectAttachment(int|string $id): void
    {
        if ($this->disabled) {
            return;
        }

        if (in_array($id, $this->selected)) {
            $this->selected = collect($this->selected)->filter(fn ($item) => $item !== $id)->toArray();
            $this->dispatch('highlight-attachment', null);
            return;
        }

        $this->selected = match ($this->multiple) {
            true => collect($this->selected)->push($id)->unique()->toArray(),
            false => [$id],
        };

        $this->dispatch('highlight-attachment', $id);
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
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Return current path in parts (breadcrumbs).
     */
    #[Computed]
    public function breadcrumbs(): array
    {
        $crumbs = array_filter(explode('/', $this->currentPath ?? ''));
        $breadcrumbs = [];

        foreach ($crumbs as $index => $crumb) {
            $pathToCrumb = implode('/', array_slice($crumbs, 0, $index + 1));
            $breadcrumbs[$pathToCrumb] = $crumb;
        }

        return $breadcrumbs;
    }

    private function getDirectories(): Collection
    {
        $sortColumn = Str::beforeLast($this->sortBy, '_');
        $sortDirection = Str::afterLast($this->sortBy, '_');

        return AttachmentManager::directories($this->currentPath)
            ->when($this->search, function (Collection $collection) {
                return $collection->filter(fn (Directory $directory) => str_contains(strtolower($directory->name), strtolower($this->search)));
            })
            ->when(!$this->search, function (Collection $collection) {
                return $collection->filter(fn (Directory $directory) => $directory->path === $this->currentPath);
            })
            ->when($sortColumn === 'name', function (Collection $collection) use ($sortDirection) {
                return $sortDirection === 'desc'
                    ? $collection->sortByDesc('name')
                    : $collection->sortBy('name');
            })->map(fn (Directory $directory) => new DirectoryViewModel($directory));
    }

    /**
     * @return LengthAwarePaginator<AttachmentViewModel>
     */
    private function getAttachments(): LengthAwarePaginator
    {
        $sortColumn = Str::beforeLast($this->sortBy, '_');
        $sortDirection = Str::afterLast($this->sortBy, '_');

        $attachments = Attachment::query()
            ->when($this->search, function (Builder $query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when(!$this->search, function (Builder $query) {
                $query->where('path', $this->currentPath);
            })
            ->when($this->mime, function (Builder $query) {
                $query->where('mime_type', 'like', str_replace('*', '%', $this->mime));
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($this->pageSize);

        $collection = $attachments->getCollection()
            ->map(fn (Attachment $attachment) => new AttachmentViewModel($attachment));

        /** @var LengthAwarePaginator<AttachmentViewModel> $attachments */
        $attachments->setCollection($collection);

        return $attachments;
    }


    #[On('close-modal')]
    public function closeModal(bool $save = false, ?string $statePath = null): void
    {
        if (!$save) {
            return;
        }

        if ($statePath !== $this->statePath) {
            return;
        }

        $selected = match ($this->multiple) {
            true => $this->selected,
            false => $this->selected[0] ?? null,
        };

        $this->dispatch('attachments-selected', statePath: $statePath, selected: $selected);
    }
}
