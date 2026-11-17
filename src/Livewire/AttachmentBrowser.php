<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\PathTraversalDetected;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Session;
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
use VanOns\FilamentAttachmentLibrary\Concerns\DispatchesToScope;
use VanOns\FilamentAttachmentLibrary\Concerns\HandlesDroppedFiles;
use VanOns\FilamentAttachmentLibrary\Concerns\InteractsWithActionsUsingAlpineJS;
use VanOns\FilamentAttachmentLibrary\Enums\DirectoryNavigation;
use VanOns\FilamentAttachmentLibrary\Enums\Layout;
use VanOns\FilamentAttachmentLibrary\Rules\MatchesFileFilter;
use VanOns\FilamentAttachmentLibrary\Support\DirectoryTree;
use VanOns\FilamentAttachmentLibrary\Support\Path;
use VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel;
use VanOns\FilamentAttachmentLibrary\ViewModels\DirectoryViewModel;
use VanOns\LaravelAttachmentLibrary\AttachmentQueryBuilder;
use VanOns\LaravelAttachmentLibrary\DataTransferObjects\Directory;
use VanOns\LaravelAttachmentLibrary\Exceptions\DestinationAlreadyExistsException;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class AttachmentBrowser extends Component implements HasActions, HasForms
{
    use DispatchesToScope;
    use HandlesDroppedFiles;
    use InteractsWithActionsUsingAlpineJS;
    use InteractsWithForms;
    use WithPagination;

    /**
     * Locked: the base path is derived server-side and is the tenancy boundary when configured
     * as a per-tenant prefix — a tampered value would expose another tenant's directories.
     */
    #[Locked]
    public ?string $basePath = null;

    /**
     * Whether browser state (path, sort, filters, page) is synced to the URL's query string.
     * The page browser tracks; the modal instance must not — two instances tracking the same
     * params fight over the query string and the hidden modal hydrates on every history pop.
     */
    #[Locked]
    public bool $trackUrl = true;

    public ?string $currentPath = null;

    /**
     * Display preferences persist per user session; shared by the page and modal instances.
     */
    #[Session]
    public string $sortBy = 'created_at_desc';

    #[Session]
    public int $pageSize = 25;

    #[Session]
    public Layout $layout = Layout::GRID;

    /**
     * Relative paths of the directories that are open in the directory tree, and whether the tree
     * itself is collapsed. Both are kept in the session, the open directories per base path so that
     * switching tenants never shows another tenant's state.
     *
     * Not #[Session]: that writes on every request of every instance, and the page and the modals
     * share these, so an instance that was not touched would put its older state back.
     *
     * @var array<int, string>
     */
    public array $expandedDirectories = [];

    public bool $treeCollapsed = false;

    public string $search = '';

    public ?string $mime = null;

    /**
     * @var array<int, string>
     */
    public array $extensions = [];

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

    public const PAGE_SIZES = [5, 10, 25, 50, 100];

    /**
     * The most attachments one drag may move, and the most open directories that are remembered.
     */
    public const MAX_MOVED_ATTACHMENTS = 100;

    public const MAX_EXPANDED_DIRECTORIES = 500;

    protected const UNDO_MOVE_SESSION_KEY = 'filament-attachment-library.undo-move';

    protected const UNDO_MOVE_DURATION = 10000;

    protected const TREE_COLLAPSED_SESSION_KEY = 'filament-attachment-library.tree-collapsed';

    public const FILTERABLE_FILE_TYPES = [
        'all' => '',
        'image' => 'image/*',
        'audio' => 'audio/*',
        'video' => 'video/*',
        'pdf' => 'application/pdf',
    ];

    /**
     * Conditional replacement for #[Url(history: true)] attributes — the modal
     * instance opts out via trackUrl. Nullability is inferred from the typehints.
     *
     * @return array<string, array{history: bool}>
     */
    protected function queryString(): array
    {
        if (!$this->trackUrl) {
            return [];
        }

        return [
            'currentPath' => ['history' => true],
            'sortBy' => ['history' => true],
            'pageSize' => ['history' => true],
            'layout' => ['history' => true],
            'mime' => ['history' => true],
        ];
    }

    /**
     * Override of the WithPagination trait hook: keep ?page out of the URL for the modal instance.
     */
    public function queryStringHandlesPagination(): array
    {
        if (!$this->trackUrl) {
            return [];
        }

        return collect($this->paginators)->mapWithKeys(function ($page, $pageName) {
            return ['paginators.' . $pageName => ['history' => true, 'as' => $pageName, 'keep' => false]];
        })->toArray();
    }

    public function render(): View
    {
        $this->currentPath = $this->normalizePath($this->currentPath);

        $attachments = $this->getAttachments();
        $directories = $this->getDirectories();
        $tree = DirectoryNavigation::configured()->isTree() ? $this->directoryTree() : null;

        // The tree has its own search for directories; this one then only finds files.
        $searchPlaceholder = $tree ? __('filament-attachment-library::views.search_files') : null;

        return view('filament-attachment-library::livewire.attachment-browser', compact('attachments', 'directories', 'tree', 'searchPlaceholder'));
    }

    public function mount(): void
    {
        if (!in_array($this->pageSize, self::PAGE_SIZES)) {
            $this->pageSize = 1;
        }

        if (!in_array($this->layout, Layout::cases())) {
            $this->layout = Layout::GRID;
        }

        [$sortColumn, $sortDirection] = $this->resolveSort();
        $sortBy = "{$sortColumn}_{$sortDirection}";

        if ($sortBy !== $this->sortBy) {
            $this->sortBy = $sortBy;
        }

        $this->restoreTreeState();

        // When lazy-loaded, mount() runs on the deferred load request; announce readiness so the
        // modal wrapper can replay an open-attachment-modal payload dispatched before the load.
        $this->dispatch('attachment-browser-loaded', scope: $this->scope);
    }

    public function createDirectoryAction(): Action
    {
        return CreateDirectoryAction::make('createDirectory')
            ->setCurrentPath($this->getCurrentPath())
            ->setHasBasePath((bool) $this->basePath);
    }

    public function deleteDirectoryAction(): Action
    {
        return DeleteDirectoryAction::make('deleteDirectory')
            ->setBasePath($this->basePath)
            ->after(fn (array $arguments) => $this->forgetDirectory($arguments['full_path'] ?? null));
    }

    public function renameDirectoryAction(): Action
    {
        return RenameDirectoryAction::make('renameDirectory')
            ->setBasePath($this->basePath)
            ->after(fn (array $arguments, array $data) => $this->followRenamedDirectory(
                $arguments['full_path'] ?? null,
                $data['name'] ?? null,
            ));
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
        return MoveAttachmentAction::make('moveAttachment')->setBasePath($this->basePath);
    }

    public function replaceAttachmentAction(): Action
    {
        return ReplaceAttachmentAction::make('replaceAttachment')
            ->setCurrentPath($this->getCurrentPath());
    }

    protected function getCurrentPath(): ?string
    {
        return $this->fullPath($this->currentPath);
    }

    /**
     * The path on the disk of a path the client navigates with, which is relative to the base path.
     */
    protected function fullPath(?string $relativePath): ?string
    {
        return Path::join(Path::sanitize($this->basePath), $this->normalizePath($relativePath));
    }

    /**
     * The path the client navigates with for a path on the disk.
     */
    public function relativePath(?string $path): ?string
    {
        return Path::relativeTo($this->normalizePath($path), $this->basePath);
    }

    /**
     * Whether a path on the disk is the base path or lies below it. Everything a client asks for by
     * path or by id has to pass this, or a tenant could reach into another tenant's directories.
     */
    protected function isWithinBasePath(?string $path): bool
    {
        return Path::isWithin($path, $this->basePath);
    }

    protected function droppedFilesPath(): ?string
    {
        return $this->getCurrentPath();
    }

    protected function droppedFileRules(): array
    {
        return [new MatchesFileFilter($this->mime, $this->extensions)];
    }

    protected function handleUploadedDrop(Attachment $attachment): void
    {
        $this->selectAttachment($attachment->id);
    }

    protected function dropsDisabled(): bool
    {
        return $this->disabled;
    }

    public function selectAttachment(int|string $id): void
    {
        if ($this->disabled) {
            return;
        }

        if (in_array($id, $this->selected)) {
            $this->selected = collect($this->selected)->filter(fn ($item) => $item !== $id)->values()->toArray();
            $this->highlight(null);
            return;
        }

        $this->selected = match ($this->multiple) {
            true => collect($this->selected)->push($id)->unique()->values()->toArray(),
            false => [$id],
        };

        $this->highlight($id);
    }

    /**
     * Refresh the info sidebar for the clicked attachment. The selection ring itself is driven
     * client-side (Alpine `attachmentSelection`); this only handles the sidebar highlight, while
     * the entangled `selected` array rides along on the same commit to keep the server in sync.
     */
    public function highlight(?int $id): void
    {
        $this->dispatchToScope('highlight-attachment', $id);
    }

    /**
     * Set current path.
     */
    #[On('open-path')]
    public function openPath(?string $path): void
    {
        $this->currentPath = $this->relativePath($path);

        $this->resetPage();

        $this->highlight(null);
    }

    /**
     * Open a directory from the tree. Unlike openPath(), which also takes paths on the disk, this
     * only takes a path below the base path: a directory named like the base path stays what it is.
     */
    public function openDirectory(?string $path = null): void
    {
        $this->currentPath = $this->normalizePath($path);

        // The tree shows where you are; search results from every directory would contradict it.
        $this->search = '';

        $this->resetPage();

        $this->highlight(null);
    }

    /**
     * Create a directory inside a directory of the tree. Opening it and asking for the name happen
     * in one request, so a click on another row in between cannot change where the directory lands.
     */
    public function createSubdirectory(?string $path = null): void
    {
        $this->openDirectory($path);

        $this->mountAction('createDirectory');
    }

    #[On('set-mime')]
    public function setMime(?string $mime): void
    {
        $this->mime = $mime;

        // updatingMime() only fires for client-side updates, not this event path.
        $this->resetPage();
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
     * Reset page on page size update — the current page may not exist at the new size.
     */
    public function updatingPageSize(): void
    {
        $this->resetPage();
    }

    /**
     * Everything the directory tree shows: its nested nodes and the number of files directly in the root.
     *
     * @return array{nodes: list<array<string, mixed>>, rootCount: int}
     */
    protected function directoryTree(): array
    {
        $counts = $this->directoryCounts();

        return [
            'nodes' => DirectoryTree::build($this->allDirectories(), $this->basePath, $counts),
            'rootCount' => $counts[(string) Path::sanitize($this->basePath)] ?? 0,
        ];
    }

    /**
     * Every directory below the base path, in one listing. The tree is read straight from the disk
     * rather than through the attachment manager, which lists one level at a time and syncs each
     * directory it passes: far too much to repeat on every render.
     *
     * Listed once per request. Nothing may ask for it before it changes a directory in that request.
     *
     * @return array<int, string>
     */
    protected function allDirectories(): array
    {
        return once(function () {
            try {
                return Storage::disk(config('attachment-library.disk', 'public'))->allDirectories($this->basePath);
            } catch (PathTraversalDetected) {
                return [];
            }
        });
    }

    /**
     * The number of files directly inside each directory, by full path, in one query.
     *
     * @return array<string, int>
     */
    protected function directoryCounts(): array
    {
        $basePath = Path::sanitize($this->basePath);

        return Attachment::query()
            // Nested: the scope ends in an orWhere.
            ->when($basePath !== null, fn (Builder $query) => $query->where(
                fn (AttachmentQueryBuilder $query) => $query->whereInPath($basePath)
            ))
            ->groupBy('path')
            // Files in the root have no path; '' keeps them from becoming a null array key.
            ->selectRaw("coalesce(path, '') as directory, count(*) as aggregate")
            ->pluck('aggregate', 'directory')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    /**
     * Called by the tree after a directory or the tree itself is opened or closed. It does nothing
     * by itself: the request carries the new state, and the updated hooks below store it.
     */
    #[Renderless]
    public function rememberTree(): void
    {
    }

    public function updatedExpandedDirectories(): void
    {
        $this->expandedDirectories = $this->sanitizeExpandedDirectories($this->expandedDirectories);

        $this->storeExpandedDirectories();
    }

    public function updatedTreeCollapsed(): void
    {
        session()->put(self::TREE_COLLAPSED_SESSION_KEY, $this->treeCollapsed);
    }

    /**
     * Take over what the tree looked like when it was last changed, in this browser or another one.
     */
    protected function restoreTreeState(): void
    {
        $this->expandedDirectories = $this->storedExpandedDirectories();
        $this->treeCollapsed = (bool) session()->get(self::TREE_COLLAPSED_SESSION_KEY, false);
    }

    /**
     * @return array<int, string>
     */
    protected function storedExpandedDirectories(): array
    {
        return $this->sanitizeExpandedDirectories(session()->get($this->expandedDirectoriesSessionKey(), []));
    }

    protected function storeExpandedDirectories(): void
    {
        session()->put($this->expandedDirectoriesSessionKey(), $this->expandedDirectories);
    }

    /**
     * The open directories arrive from the client and from the session: keep clean relative paths only.
     *
     * @return array<int, string>
     */
    protected function sanitizeExpandedDirectories(mixed $paths): array
    {
        return collect(Arr::wrap($paths))
            ->filter(fn ($path) => is_string($path))
            ->map(fn (string $path) => Path::sanitize($path))
            ->filter()
            ->unique()
            ->take(self::MAX_EXPANDED_DIRECTORIES)
            ->values()
            ->all();
    }

    protected function expandedDirectoriesSessionKey(): string
    {
        return 'filament-attachment-library.expanded-directories.' . md5((string) $this->basePath);
    }

    /**
     * Move attachments into a directory, as dropped on it in the tree or on a directory card.
     *
     * @param  array<int, int|string>  $ids
     * @param  string|null  $path  Relative to the base path; null for the root.
     */
    public function moveAttachments(array $ids, ?string $path = null): void
    {
        if ($this->disabled) {
            return;
        }

        $path = $this->normalizePath($path);
        $target = $this->fullPath($path);

        // Only the base path itself or a directory the tree lists: nothing the client can make up.
        if ($path !== null && ! in_array($target, $this->allDirectories(), true)) {
            Notification::make()
                ->title(__('filament-attachment-library::validation.invalid_path'))
                ->danger()
                ->send();

            return;
        }

        $ids = collect($ids)->map(fn ($id) => (int) $id)->filter()->unique()->take(self::MAX_MOVED_ATTACHMENTS);

        $moved = [];
        $conflicts = 0;

        foreach (Attachment::query()->whereKey($ids->all())->get() as $attachment) {
            if (! $this->isWithinBasePath($attachment->path) || $attachment->path === $target) {
                continue;
            }

            $origin = $attachment->path;

            try {
                AttachmentManager::move($attachment, $target);

                $moved[$attachment->id] = $origin;
            } catch (DestinationAlreadyExistsException) {
                $conflicts++;
            }
        }

        $directory = $path === null
            ? __('filament-attachment-library::views.home')
            : Path::name($path);

        if ($conflicts > 0) {
            Notification::make()
                ->title(trans_choice('filament-attachment-library::notifications.attachment.move_conflict', $conflicts, [
                    'count' => $conflicts,
                    'directory' => $directory,
                ]))
                ->danger()
                ->send();
        }

        if ($moved === []) {
            return;
        }

        $token = (string) Str::uuid();
        session()->put(self::UNDO_MOVE_SESSION_KEY, ['token' => $token, 'moves' => $moved]);

        // The selection stays: a moved attachment is still the same attachment to a field that picked it.
        $this->highlight(null);

        Notification::make()
            ->title(trans_choice('filament-attachment-library::notifications.attachment.moved_to', count($moved), [
                'count' => count($moved),
                'directory' => $directory,
            ]))
            ->success()
            ->actions([
                Action::make('undo')
                    ->label(__('filament-attachment-library::views.actions.attachment.undo'))
                    ->button()
                    ->color('gray')
                    ->dispatch("undo-attachment-move.{$this->scope}", ['token' => $token])
                    ->close(),
            ])
            // Longer than a plain notification: this is the only chance to undo.
            ->duration(self::UNDO_MOVE_DURATION)
            ->send();
    }

    /**
     * Put the attachments of the last move back. Only that move is kept, and only on the server:
     * the client hands back a token, never the paths.
     */
    #[On('undo-attachment-move.{scope}')]
    public function undoMove(string $token): void
    {
        $undo = session()->get(self::UNDO_MOVE_SESSION_KEY);

        if (! is_array($undo) || ($undo['token'] ?? null) !== $token) {
            Notification::make()
                ->title(__('filament-attachment-library::notifications.attachment.move_undo_expired'))
                ->warning()
                ->send();

            return;
        }

        session()->forget(self::UNDO_MOVE_SESSION_KEY);

        $attachments = Attachment::query()->whereKey(array_keys($undo['moves']))->get()->keyBy('id');
        $restored = 0;

        foreach ($undo['moves'] as $id => $origin) {
            $attachment = $attachments->get($id);

            if (! $attachment || ! $this->isWithinBasePath($attachment->path) || ! $this->isWithinBasePath($origin)) {
                continue;
            }

            try {
                AttachmentManager::move($attachment, $origin);

                $restored++;
            } catch (DestinationAlreadyExistsException) {
                // Something else took the old place in the meantime; the file stays where it is.
            }
        }

        if ($restored < count($undo['moves'])) {
            Notification::make()
                ->title(__('filament-attachment-library::notifications.attachment.move_undo_incomplete'))
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title(__('filament-attachment-library::notifications.attachment.move_undone'))
            ->success()
            ->send();
    }

    /**
     * The tree can delete the directory that is open, or one above it: step out to what is left.
     */
    protected function forgetDirectory(?string $fullPath): void
    {
        $deleted = $this->relativePath($fullPath);

        if ($deleted === null) {
            return;
        }

        // From what is stored, not from this instance: another browser may have changed the tree since.
        $this->expandedDirectories = array_values(array_filter(
            $this->storedExpandedDirectories(),
            fn (string $path) => ! Path::isWithin($path, $deleted),
        ));
        $this->storeExpandedDirectories();

        if (Path::isWithin($this->currentPath, $deleted)) {
            $this->openDirectory(Path::parent($deleted));
        }
    }

    /**
     * The tree can rename the directory that is open, or one above it: keep pointing at the same directory.
     */
    protected function followRenamedDirectory(?string $fullPath, ?string $name): void
    {
        $old = $this->relativePath($fullPath);

        if ($old === null || blank($name)) {
            return;
        }

        $new = Path::join(Path::parent($old), $name);

        $rename = fn (?string $path) => Path::isWithin($path, $old) ? $new . substr($path, strlen($old)) : $path;

        $this->expandedDirectories = array_values(array_map($rename, $this->storedExpandedDirectories()));
        $this->storeExpandedDirectories();
        $this->currentPath = $rename($this->currentPath);
    }

    /**
     * Normalize path to ensure empty strings are treated as null (root directory).
     */
    public function normalizePath(?string $path): ?string
    {
        return Path::sanitize($path);
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

    /**
     * Whitelists $sortBy against SORTABLE_FIELDS before it ever reaches a query —
     * $sortBy is user-controlled (session + URL), so an unvalidated column/direction
     * here is a SQL injection vector via orderBy() in getAttachments().
     *
     * @return array{0: string, 1: 'asc'|'desc'}
     */
    private function resolveSort(): array
    {
        $column = Str::beforeLast($this->sortBy, '_');
        $direction = Str::afterLast($this->sortBy, '_');

        if (!in_array($column, self::SORTABLE_FIELDS, true) || !in_array($direction, ['asc', 'desc'], true)) {
            return ['created_at', 'desc'];
        }

        return [$column, $direction];
    }

    private function getDirectories(): Collection
    {
        try {
            $directories = AttachmentManager::directories($this->getCurrentPath());
        } catch (PathTraversalDetected) {
            Notification::make()
                ->title(__('filament-attachment-library::validation.invalid_path'))
                ->danger()
                ->send();

            return collect();
        }

        return $directories
            ->when($this->search, function (Collection $collection) {
                return $collection->filter(fn (Directory $directory) => str_contains(strtolower($directory->name), strtolower($this->search)));
            })
            ->when(!$this->search, function (Collection $collection) {
                return $collection->filter(fn (Directory $directory) => $directory->path === $this->getCurrentPath());
            })
            // Always A to Z and never by case, whatever the files are sorted by.
            ->sortBy(fn (Directory $directory) => $directory->name, SORT_NATURAL | SORT_FLAG_CASE)
            ->map(fn (Directory $directory) => new DirectoryViewModel($directory));
    }

    /**
     * @return LengthAwarePaginator<int, AttachmentViewModel>
     */
    private function getAttachments(): LengthAwarePaginator
    {
        [$sortColumn, $sortDirection] = $this->resolveSort();

        $attachments = Attachment::query()
            ->with('poster')
            ->when($this->search, function (Builder $query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when(!$this->search, function (Builder $query) {
                $query->where('path', $this->getCurrentPath());
            })
            ->when($this->mime, function (Builder $query) {
                $query->where('mime_type', 'like', str_replace('*', '%', $this->mime));
            })
            ->when($this->extensions, function (Builder $query) {
                $query->whereIn(DB::raw('lower(extension)'), $this->extensions);
            })
            // lower(): whether a plain order by name ignores case depends on the database and its collation.
            ->orderBy($sortColumn === 'name' ? DB::raw('lower(name)') : $sortColumn, $sortDirection)
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
        // action modals and the other browser levels); only react to this browser's own modal.
        if ($id !== $this->scope) {
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

        $this->highlight(null);

        // Reset everything except mount-time config (reset() falls back to class defaults,
        // which would re-enable URL tracking and drop the tenant base path) and the
        // session-persisted display preferences (resetting would clobber the stored values).
        $this->reset(array_diff(array_keys($this->all()), ['basePath', 'trackUrl', 'scope', 'sortBy', 'pageSize', 'layout', 'expandedDirectories', 'treeCollapsed']));
    }

    /**
     * Open this browser as a picker. Payloads for other browser levels are ignored; a payload
     * without a modal id targets the first level, for code that opens the modal itself.
     */
    #[On('open-attachment-modal')]
    public function openModal(?string $statePath = null, int|array|null $selected = null, ?bool $multiple = null, ?string $mime = null, ?bool $disableMimeFilter = null, int|string|null $highlight = null, ?string $modalId = null, ?array $extensions = null): void
    {
        if (($modalId ?? AttachmentModalStack::modalId(0)) !== $this->scope) {
            return;
        }

        // This browser may have been loaded long before it is opened.
        $this->restoreTreeState();

        $this->statePath = $statePath;
        $this->multiple = $multiple ?? false;
        $this->mime = $mime;
        $this->extensions = $extensions ?? [];
        $this->disableMimeFilter = $disableMimeFilter ?? false;

        $this->selected = Arr::wrap($selected);

        // Dispatched server-side so it also works on the lazy first load, where the
        // payload arrives via the modal wrapper's replay.
        if ($highlight) {
            $this->highlight((int) $highlight);
        }
    }
}
