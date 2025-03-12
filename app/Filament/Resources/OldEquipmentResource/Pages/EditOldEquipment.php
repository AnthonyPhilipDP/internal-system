<?php

namespace App\Filament\Resources\OldEquipmentResource\Pages;

use App\Filament\Resources\OldEquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOldEquipment extends EditRecord
{
    protected static string $resource = OldEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
