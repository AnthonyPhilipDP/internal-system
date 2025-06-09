<?php

namespace App\Filament\Resources\ClientExclusiveResource\Pages;

use App\Filament\Resources\ClientExclusiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientExclusives extends ListRecords
{
    protected static string $resource = ClientExclusiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
