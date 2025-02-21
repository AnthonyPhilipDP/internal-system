<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions\Action;
use Filament\Actions;
use Filament\Support\Enums\Alignment;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\EquipmentResource;

class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Add New Equipment'),
            Action::make('acknowledgmentReceipt')
                ->label('Acknowledgment Receipt')
                ->url('/ar')
        ];
    }
}
