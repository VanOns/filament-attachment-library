<?php

namespace VanOns\FilamentAttachmentLibrary\Forms\Traits;

use Illuminate\Database\Eloquent\Model;

trait HandlesFormAttachments
{
    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        return $this->syncAttachments($record, $data);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $this->syncAttachments($record, $data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->retrieveAttachments($data);
    }

    protected function retrieveAttachments(
        array $data,
        string $relationShip = 'attachments',
    ): array {
        return array_merge($data, [
            $relationShip => static::getModel()::find($data['id'])
                ->{$relationShip}
                ->pluck('id')
                ->toArray(),
        ]);
    }

    protected function syncAttachments(
        Model $record,
        array $data,
        string $relationShip = 'attachments',
        ?string $fieldName = null
    ): Model {
        return $record->{$relationShip}()->sync($data[$fieldName ?? $relationShip]);
    }
}
