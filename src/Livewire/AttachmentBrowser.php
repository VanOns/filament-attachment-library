<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use VanOns\FilamentAttachmentLibrary\Actions\CreateDirectoryAction;
use VanOns\FilamentAttachmentLibrary\Actions\DeleteAttachmentAction;
use VanOns\FilamentAttachmentLibrary\Actions\DeleteDirectoryAction;
use VanOns\FilamentAttachmentLibrary\Actions\EditAttachmentAction;
use VanOns\FilamentAttachmentLibrary\Actions\MoveAttachmentAction;
use VanOns\FilamentAttachmentLibrary\Actions\OpenAttachmentAction;
use VanOns\FilamentAttachmentLibrary\Actions\RenameDirectoryAction;
use VanOns\FilamentAttachmentLibrary\Actions\ReplaceAttachmentAction;
use VanOns\FilamentAttachmentLibrary\Actions\UploadAttachmentsAction;
use VanOns\FilamentAttachmentLibrary\Concerns\InteractsWithActionsUsingAlpineJS;
use VanOns\FilamentAttachmentLibrary\Enums\Layout;
use VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel;
use VanOns\FilamentAttachmentLibrary\ViewModels\DirectoryViewModel;
use VanOns\LaravelAttachmentLibrary\DataTransferObjects\Directory;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class AttachmentBrowser extends Component implements HasActions, HasForms
{
    use InteractsWithActionsUsingAlpineJS;
    use InteractsWithForms;
    use WithPagination;

    public ?string $basePath = null;

    #[Url(history: true, nullable: true)]
    public ?string $currentPath = null;

    #[Url(history: true)]
    public string $sortBy = 'name_asc';

    #[Url(history: true)]
    public int $pageSize = 25;

    #[Url(history: true)]
    public Layout $layout = Layout::GRID;

    public string $search = '';

    public ?string $mime = null;

    public bool $disableMimeFilter = false;

    public bool $multiple = false;

    public array $selected = [];

    public bool $disabled = false;

    public ?string $statePath = null;

    protected $listeners = [
        'refresh-attachments' => '$refresh',
    ];

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
        $this->currentPath = $this->normalizePath($this->currentPath);

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

        // When lazy-loaded, mount() runs on the deferred load request; announce readiness so the
        // modal wrapper can replay an open-attachment-modal payload dispatched before the load.
        $this->dispatch('attachment-browser-loaded');
    }

    public function uploadAttachmentsAction(): Action
    {
        return UploadAttachmentsAction::make('uploadAttachments')
            ->setCurrentPath($this->getCurrentPath());
    }

    public function createDirectoryAction(): Action
    {
        return CreateDirectoryAction::make('createDirectory')
            ->setCurrentPath($this->getCurrentPath())
            ->setHasBasePath((bool) $this->basePath);
    }

    public function deleteDirectoryAction(): Action
    {
        return DeleteDirectoryAction::make('renameDirectory');
    }

    public function renameDirectoryAction(): Action
    {
        return RenameDirectoryAction::make('renameDirectory')
            ->setCurrentPath($this->getCurrentPath());
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
            ->setCurrentPath($this->getCurrentPath());
    }

    public function moveAttachmentAction(): Action
    {
        return MoveAttachmentAction::make('moveAttachment');
    }

    public function replaceAttachmentAction(): Action
    {
        return ReplaceAttachmentAction::make('replaceAttachment')
            ->setCurrentPath($this->getCurrentPath());
    }

    protected function getCurrentPath(): ?string
    {
        return implode('/', array_filter([$this->basePath, $this->currentPath])) ?: null;
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
        $this->currentPath = Str::startsWith($path, $this->basePath)
            ? trim(Str::after($path, $this->basePath), '/')
            : $path;

        $this->resetPage();

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
     * Reset page on mime filter update.
     */
    public function updatingMime(): void
    {
        $this->resetPage();
    }

    /**
     * Normalize path to ensure empty strings are treated as null (root directory).
     */
    public function normalizePath(?string $path): ?string
    {
        $path = trim($path, '/');
        return blank($path)
            ? null
            : $path;
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

        return AttachmentManager::directories($this->getCurrentPath())
            ->when($this->search, function (Collection $collection) {
                return $collection->filter(fn (Directory $directory) => str_contains(strtolower($directory->name), strtolower($this->search)));
            })
            ->when(!$this->search, function (Collection $collection) {
                return $collection->filter(fn (Directory $directory) => $directory->path === $this->getCurrentPath());
            })
            ->when($sortColumn === 'name', function (Collection $collection) use ($sortDirection) {
                return $sortDirection === 'desc'
                    ? $collection->sortByDesc('name')
                    : $collection->sortBy('name');
            })->map(fn (Directory $directory) => new DirectoryViewModel($directory));
    }

    /**
     * @return LengthAwarePaginator<int, AttachmentViewModel>
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
                $query->where('path', $this->getCurrentPath());
            })
            ->when($this->mime, function (Builder $query) {
                $query->where('mime_type', 'like', str_replace('*', '%', $this->mime));
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($this->pageSize);

        $collection = $attachments->getCollection()
            ->map(fn (Attachment $attachment) => new AttachmentViewModel($attachment));

        /** @var LengthAwarePaginator<int, AttachmentViewModel> $attachments */
        $attachments->setCollection($collection);

        return $attachments;
    }


    #[On('close-modal')]
    public function closeModal(?string $id = null, bool $save = false): void
    {
        // Filament dispatches close-modal for every modal on the page (e.g. the edit/move/replace
        // action modals); only react to the attachment browser modal itself.
        if ($id !== 'attachment-modal') {
            return;
        }

        if ($save) {
            $selected = match ($this->multiple) {
                true => $this->selected,
                false => $this->selected[0] ?? null,
            };

            // Fire a dynamic event name based on the statePath so only the correct listener picks it up
            $this->dispatch('attachments-selected-' . md5($this->statePath), statePath: $this->statePath, selected: $selected);
        }

        $this->dispatch('highlight-attachment', null);
        $this->reset();
    }

    #[On('open-attachment-modal')]
    public function openModal(?string $statePath = null, int|array|null $selected = null, ?bool $multiple = null, ?string $mime = null, ?bool $disableMimeFilter = null, int|string|null $highlight = null): void
    {
        $this->statePath = $statePath;
        $this->multiple = $multiple;
        $this->mime = $mime;
        $this->disableMimeFilter = $disableMimeFilter;

        if ($selected) {
            $this->selected = is_array($selected) ? $selected : [$selected];
        }

        // Dispatched server-side so it also works on the lazy first load, where the
        // payload arrives via the modal wrapper's replay.
        if ($highlight) {
            $this->dispatch('highlight-attachment', id: $highlight);
        }
    }
}
