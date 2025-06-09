<?php

namespace App\Filament\Resources\ClientExclusiveResource\Pages;

use App\Filament\Resources\ClientExclusiveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientExclusive extends EditRecord
{
    protected static string $resource = ClientExclusiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
