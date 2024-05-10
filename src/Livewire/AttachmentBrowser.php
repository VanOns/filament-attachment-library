<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithPagination;
use VanOns\FilamentAttachmentLibrary\Concerns\InteractsWithActionsUsingAlpineJS;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\LaravelAttachmentLibrary\Facades\AttachmentManager;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class AttachmentBrowser extends Component implements HasActions, HasForms
{
    use InteractsWithActionsUsingAlpineJS;
    use InteractsWithForms;
    use WithPagination;

    public string $viewType = '';

    #[Url(history: true, keep: true)]
    public ?string $currentPath = null;

    public string $sortBy = 'name_ascending';

    public int $pageSize = 25;

    public string $search = '';

    public ?bool $showActions;

    public ?bool $multiple;

    protected string $view = 'filament-attachment-library::livewire.attachment-browser';

    public function mount(bool $multiple = false, bool $showActions = false): void
    {
        $this->multiple = $multiple;
        $this->showActions = $showActions;
    }

    public function deleteDirectoryAction(): Action
    {
        return Action::make('deleteDirectory')->requiresConfirmation()->color('danger')->action(
            function (array $arguments) {
                AttachmentManager::deleteDirectory($arguments['directory']['fullPath']);
            }
        );
    }

    public function renameDirectoryAction(): Action
    {
        return Action::make('renameDirectory')
            ->outlined()
            ->form([
                TextInput::make('name')
                    ->rule(new DestinationExists($this->currentPath)),
            ])
            ->mountUsing(fn (ComponentContainer $form, array $arguments) => $form->fill([
                'name' => $arguments['directory']['name'],
            ]))
            ->action(function (array $data, array $arguments) {
                AttachmentManager::renameDirectory($arguments['directory']['fullPath'], $data['name']);
            });
    }

    public function deleteAttachmentAction(): Action
    {
        return Action::make('deleteAttachment')->requiresConfirmation()->color('danger')->action(
            function (array $arguments) {
                $this->dispatch('dehighlight-attachment', $arguments['attachment_id']);

                AttachmentManager::delete(Attachment::find($arguments['attachment_id']));
            }
        );
    }

    public function renameAttachmentAction(): Action
    {
        return Action::make('renameAttachment')
            ->color('gray')
            ->form([
                TextInput::make('name')->rule(new DestinationExists($this->currentPath)),
            ])
            ->mountUsing(fn (ComponentContainer $form, array $arguments) => $form->fill([
                'name' => Attachment::find($arguments['attachment_id'])->name,
            ]))
            ->action(function (array $data, array $arguments) {
                $attachment = Attachment::find($arguments['attachment_id']);
                AttachmentManager::rename($attachment, $data['name']);
            });
    }

    public function openAttachmentAction(): Action
    {
        return Action::make('openAttachment')->color('gray')->url(
            fn (array $arguments) => Attachment::find($arguments['attachment_id'])->url
        )->openUrlInNewTab();
    }

    public function uploadAttachmentAction(): Action
    {
        return Action::make('uploadAttachment')
            ->icon('heroicon-o-arrow-up-tray')
            ->form([
                FileUpload::make('attachment')
                    ->multiple()
                    ->rule(new DestinationExists($this->currentPath))
                    ->fetchFileInformation()
                    ->saveUploadedFileUsing(
                        function (BaseFileUpload $component, TemporaryUploadedFile $file) {
                            $attachment = AttachmentManager::upload($file, $this->currentPath);
                            $this->dispatch('highlight-attachment', $attachment->id);
                            $file->delete();
                        }
                    ),
            ]);
    }

    public function createDirectoryAction(): Action
    {
        return Action::make('createDirectory')->label('Maak map')
            ->form([
                TextInput::make('name')->rules([new DestinationExists($this->currentPath)]),
            ])
            ->outlined()
            ->icon('heroicon-o-folder-plus')
            ->action(function (array $data) {
                $path = implode('/', (array_filter([$this->currentPath, $data['name']])));
                AttachmentManager::createDirectory($path);
            });
    }

    /**
     * Open directory
     */
    #[On('open-path')]
    public function openPath(?string $path): void
    {
        $this->currentPath = $path;
        $this->dispatch('highlight-attachment',  null);
    }

    public function render()
    {
        return view($this->view.$this->viewType, [
            'breadcrumbs' => $this->getCurrentPathBreadcrumbs(),
            'items' => $this->getItems(),
        ]);
    }

    /**
     * Return current path in parts (breadcrumbs)
     */
    public function getCurrentPathBreadcrumbs(): array
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
     * Return sorted and filtered attachments as paginator
     */
    protected function getItems(): LengthAwarePaginator
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

        $pageItems = $items->skip($this->pageSize * ($this->getPage() - 1))->take($this->pageSize);

        return new LengthAwarePaginator($pageItems, count($items), $this->pageSize, $this->getPage());
    }

    /**
     * Return filtered attachments
     */
    private function applyFiltering(Collection $items): Collection
    {
        if ($this->search) {
            $items = $items->filter(fn ($item) => str_contains(strtolower($item->name), strtolower($this->search)));
        }

        return $items;
    }

    /**
     * Return sorted attachments
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
