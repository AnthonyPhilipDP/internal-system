<?php

namespace App\Filament\Resources\CapabilityResource\Pages;

use App\Filament\Resources\CapabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCapability extends EditRecord
{
    protected static string $resource = CapabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
