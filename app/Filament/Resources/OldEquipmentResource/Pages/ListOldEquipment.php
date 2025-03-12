<?php

namespace App\Filament\Resources\OldEquipmentResource\Pages;

use App\Filament\Resources\OldEquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOldEquipment extends ListRecords
{
    protected static string $resource = OldEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
