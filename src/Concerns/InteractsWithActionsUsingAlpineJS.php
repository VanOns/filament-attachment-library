<?php

namespace VanOns\FilamentAttachmentLibrary\Concerns;

use Filament\Actions\Concerns\InteractsWithActions;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;

trait InteractsWithActionsUsingAlpineJS
{
    use InteractsWithActions;

    #[On('mount-action')]
    public function mountActionUsingAlpine($name, $arguments): void
    {
        $this->mountAction($name, $arguments);
    }
}
