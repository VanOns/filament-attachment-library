<?php

namespace VanOns\FilamentAttachmentLibrary\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Renders one attachment browser modal per nesting level. A picker opened while a browser modal
 * is already open (e.g. from the edit action inside it) gets the next level, so every level keeps
 * its own browser state. Levels are added on demand, up to MAX_LEVELS, and kept for reuse.
 */
class AttachmentModalStack extends Component
{
    public const MAX_LEVELS = 5;

    /**
     * Locked: the base path is the tenancy boundary, see AttachmentBrowser::$basePath.
     */
    #[Locked]
    public ?string $basePath = null;

    #[Locked]
    public int $levels = 1;

    public static function modalId(int $level): string
    {
        return $level === 0 ? 'attachment-modal' : "attachment-modal-{$level}";
    }

    #[On('grow-attachment-modal-stack')]
    public function grow(int $level): void
    {
        $this->levels = min(self::MAX_LEVELS, max($this->levels, $level + 1));
    }

    public function render(): View
    {
        return view('filament-attachment-library::livewire.attachment-modal-stack');
    }
}
