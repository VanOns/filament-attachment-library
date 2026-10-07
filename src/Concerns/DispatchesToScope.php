<?php

namespace VanOns\FilamentAttachmentLibrary\Concerns;

use Livewire\Attributes\Locked;

/**
 * Scopes events to one attachment browser and its info panels, so stacked browsers
 * (the library page and every modal level) don't react to each other's events.
 */
trait DispatchesToScope
{
    #[Locked]
    public string $scope = 'page';

    public function dispatchToScope(string $event, mixed ...$params): void
    {
        $this->dispatch("{$event}.{$this->scope}", ...$params);
    }
}
