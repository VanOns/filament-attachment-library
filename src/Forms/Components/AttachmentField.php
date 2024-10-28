<?php

namespace VanOns\FilamentAttachmentLibrary\Forms\Components;

use Filament\Forms\Components\Concerns\CanLimitItemsLength;
use Filament\Forms\Components\Field;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as LaravelView;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class AttachmentField extends Field
{
    use CanLimitItemsLength;

    public bool $multiple = false;

    public bool $showActions = false;

    public string $mime = '';

    protected string $view = 'filament-attachment-library::forms.components.attachment-field';

    /**
     * Return all selected attachments modals from state
     */
    public function getAttachments(): Collection|Attachment
    {
        $attachments = Attachment::find($this->getState());

        if ($attachments instanceof Attachment) {
            $attachments = [$attachments];
        }

        return collect($attachments);
    }

    /**
     * Return selected attachments and return first if multiple is false
     */
    public function getState(): mixed
    {
        $state = collect(parent::getState())->unique();

        if ($this->multiple) {
            return $state;
        }

        return $state->first();
    }

    /**
     * Allow the selection of multiple attachments
     */
    public function multiple(): Field
    {
        $this->multiple = true;

        return $this;
    }

    public function getMultiple(): bool
    {
        return $this->evaluate($this->multiple);
    }

    public function mime(string $mimeType): Field
    {
        $this->mime = $mimeType;

        return $this;
    }

    public function getMime(): string
    {
        return $this->evaluate($this->mime);
    }

    /**
     * Wrapper methods to stay compliant with commonly used FileUpload methods.
     */
    public function minFiles(int $min): Field
    {
        return $this->minItems($min);
    }

    public function maxFiles(int $max): Field
    {
        return $this->maxItems($max);
    }

    public function image(): Field
    {
        return $this->mime('image/*');
    }

    /**
     * Wrapper methods for restricting mime types.
     */
    public function audio(): Field
    {
        return $this->mime('audio/*');
    }

    public function video(): Field
    {
        return $this->mime('video/*');
    }

    public function text(): Field
    {
        return $this->mime('text/*');
    }

    public function render(): View
    {
        // Activate render of browser modal
        LaravelView::share('renderAttachmentBrowserModal', true);

        return parent::render();
    }
}
