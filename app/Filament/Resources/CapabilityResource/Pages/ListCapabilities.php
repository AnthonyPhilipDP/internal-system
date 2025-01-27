<?php

namespace App\Filament\Resources\CapabilityResource\Pages;

use App\Filament\Resources\CapabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCapabilities extends ListRecords
{
    protected static string $resource = CapabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
