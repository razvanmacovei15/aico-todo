<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Sync users relation before saving the rest of the model data
        if (isset($data['users'])) {
            $this->record->users()->sync($data['users']);
            unset($data['users']); // remove so mass assign doesn't error
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
