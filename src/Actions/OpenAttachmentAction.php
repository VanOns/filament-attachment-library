<?php

namespace VanOns\FilamentAttachmentLibrary\Actions;

use Filament\Actions\Action;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class OpenAttachmentAction extends Action
{
    protected function setUp(): void
    {
        $this->color('gray');

        $this->url(function (array $arguments) {
            /** @var Attachment $attachment */
            $attachment = Attachment::find($arguments['attachment_id']);

            return $attachment->url;
        });

        $this->openUrlInNewTab();
    }
}
