<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class OpenAttachmentAction extends Action
{
    protected function setUp(): void
    {
        $this->color('gray');

        $this->url(fn (array $arguments) => Attachment::find($arguments['attachment_id'])->url);

        $this->openUrlInNewTab();
    }
}
