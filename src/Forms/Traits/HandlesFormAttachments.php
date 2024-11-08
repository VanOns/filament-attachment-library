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

    protected function syncAttachments(
        Model   $record,
        array   $data,
        string  $relationship = 'attachments',
        ?string $fieldName = null
    ): Model {
        return $record->{$relationship}()->sync($data[$fieldName ?? $relationship]);
    }

    protected function retrieveAttachments(
        array  $data,
        string $relationship = 'attachments',
    ): array {
        return array_merge($data, [
            $relationship => static::getModel()::find($data['id'])
                ->{$relationship}
                ->pluck('id')
                ->toArray(),
        ]);
    }
}
