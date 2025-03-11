<?php

namespace App\Filament\Resources\EquipmentOldResource\Pages;

use App\Filament\Resources\EquipmentOldResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEquipmentOld extends EditRecord
{
    protected static string $resource = EquipmentOldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
