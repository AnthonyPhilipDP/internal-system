<?php

namespace App\Filament\Resources\EquipmentOldResource\Pages;

use App\Filament\Resources\EquipmentOldResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEquipmentOlds extends ListRecords
{
    protected static string $resource = EquipmentOldResource::class;

    protected static ?string $title = 'Old Equipment';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
