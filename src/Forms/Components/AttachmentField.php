<?php

namespace VanOns\FilamentAttachmentLibrary\Forms\Components;

use Closure;
use Filament\Forms\Components\Concerns\CanLimitItemsLength;
use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use ReflectionProperty;
use VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel;
use VanOns\LaravelAttachmentLibrary\Facades\Glide;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

class AttachmentField extends Field
{
    use CanLimitItemsLength;

    public bool|Closure $multiple = false;

    public bool|Closure $reorderable = true;

    public ?string $collection;

    public ?string $relationship = null;

    public bool $showActions = false;

    public ?string $mime = null;

    protected string $view = 'filament-attachment-library::forms.components.attachment-field';

    protected function setUp(): void
    {
        parent::setup();

        $this->helperText(function () {
            if (empty($formats = Glide::getSupportedImageFormats())) {
                return null;
            }

            return __('filament-attachment-library::forms.attachment_field.help', [
                'types' => implode(', ', $formats),
            ]);
        });
    }

    /**
     * Return all selected attachments from state, preserving the state order.
     */
    public function getAttachments(): Collection
    {
        $ids = collect($this->getState())->filter()->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $attachmentMap = Attachment::find($ids->toArray())->keyBy('id');

        return $ids
            ->map(fn ($id) => $attachmentMap->get($id))
            ->filter()
            ->map(fn ($model) => new AttachmentViewModel($model));
    }

    /**
     * Return selected attachments and return first if multiple is false.
     */
    public function getState(): mixed
    {
        $state = collect(parent::getState())->unique();

        if ($this->getMultiple()) {
            return $state;
        }

        return $state->first();
    }

    public function collection(?string $collection = null): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function relationship(string $relationship = 'attachments'): static
    {
        // We check if the property has been initialized to allow setting the collection before the relationship.
        // We have to use reflection because the property can be null and other checks fail in this case.
        if (!(new ReflectionProperty(static::class, 'collection'))->isInitialized($this)) {
            $this->collection = $this->getName();
        }

        $this->relationship = $relationship;

        $this->dehydrated(false);

        $this->loadStateFromRelationshipsUsing(
            function (AttachmentField $component, Model $record, $state) {
                if (filled($state)) {
                    return;
                }

                $relationship = $record->{$this->relationship}();

                if ($relationship instanceof MorphToMany) {
                    $state = $relationship->where('collection', $this->collection)->pluck(
                        $relationship->getRelatedKeyName()
                    )->all();

                    $component->state($state);
                }
            }
        );

        $this->saveRelationshipsUsing(
            function (Model $record, $state): void {
                $state = match ($state instanceof Collection) {
                    true => $state,
                    default => collect([$state])->filter()
                };

                $record->{$this->relationship}()->sync(
                    $state->mapWithKeys(fn ($attachmentId, $index) => [
                        $attachmentId => $this->getReorderable()
                            ? ['collection' => $this->collection, 'order' => $index]
                            : ['collection' => $this->collection],
                    ])
                );
            }
        );

        return $this;
    }

    /**
     * Allow the selection of multiple attachments.
     */
    public function multiple(bool|Closure $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getMultiple(): bool
    {
        return $this->evaluate($this->multiple);
    }

    /**
     * Allow drag-and-drop reordering of selected attachments.
     * When used with a relationship, requires the `order` column on the pivot table.
     * Publish and run the package migration: vendor:publish --tag=filament-attachment-library-migrations
     */
    public function reorderable(bool|Closure $reorderable = true): static
    {
        $this->reorderable = $reorderable;

        return $this;
    }

    public function getReorderable(): bool
    {
        return $this->getMultiple() && $this->evaluate($this->reorderable);
    }

    public function mime(string $mimeType): static
    {
        $this->mime = $mimeType;

        return $this;
    }

    public function getMime(): ?string
    {
        return $this->evaluate($this->mime);
    }

    /**
     * Wrapper methods to stay compliant with commonly used FileUpload methods.
     */

    public function minFiles(int $min): static
    {
        return $this->minItems($min);
    }

    public function maxFiles(int $max): static
    {
        return $this->maxItems($max);
    }

    public function image(): static
    {
        return $this->mime('image/*');
    }

    /**
     * Wrapper methods for restricting mime types.
     */
    public function audio(): static
    {
        return $this->mime('audio/*');
    }

    public function video(): static
    {
        return $this->mime('video/*');
    }

    public function text(): static
    {
        return $this->mime('text/*');
    }
}
