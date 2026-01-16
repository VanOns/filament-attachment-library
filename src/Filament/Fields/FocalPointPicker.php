<?php

namespace VanOns\FilamentAttachmentLibrary\Filament\Fields;

use Closure;
use Filament\Forms\Components\Field;

class FocalPointPicker extends Field
{
    protected string | Closure | null $image;


    protected string $view = 'filament-attachment-library::fields.focal-point-picker';

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(function ($state) {
            if (!$state) {
                return [ 'x' => 50, 'y' => 50 ];
            }

            return $state;
        });
    }

    public function image(string | Closure | null $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->evaluate($this->image);
    }
}
