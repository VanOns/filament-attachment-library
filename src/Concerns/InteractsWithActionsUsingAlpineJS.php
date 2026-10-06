<?php

namespace VanOns\FilamentAttachmentLibrary\Concerns;

use Filament\Actions\Concerns\InteractsWithActions;
use Livewire\Attributes\On;

/**
 * Requires DispatchesToScope: actions are mounted from the info panels of the same scope.
 */
trait InteractsWithActionsUsingAlpineJS
{
    use InteractsWithActions;

    #[On('mount-action.{scope}')]
    public function mountActionUsingAlpine($name, $arguments): void
    {
        $this->mountAction($name, $arguments);
    }
}
