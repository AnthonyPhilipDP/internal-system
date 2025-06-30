<?php

namespace App\Filament\Resources\PotentialCustomerResource\Pages;

use App\Filament\Resources\PotentialCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPotentialCustomers extends ListRecords
{
    protected static string $resource = PotentialCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Add New Potential Customer')
            ->icon('heroicon-o-user-plus'),
        ];
    }
}
