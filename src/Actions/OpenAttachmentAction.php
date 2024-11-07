<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class OpenAttachmentAction extends Action
{
    protected function setUp(): void
    {
        $arguments = $this->getArguments();

        /**
         * @var Attachment $attachment
         */
        $attachment = Attachment::find($arguments['attachment_id']);

        $this->color('gray');

        $this->url(fn (array $arguments) => $attachment->url);

        $this->openUrlInNewTab();
    }
}
