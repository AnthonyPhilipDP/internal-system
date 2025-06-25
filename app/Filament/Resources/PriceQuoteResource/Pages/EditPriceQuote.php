<?php

namespace App\Filament\Resources\PriceQuoteResource\Pages;

use App\Filament\Resources\PriceQuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPriceQuote extends EditRecord
{
    protected static string $resource = PriceQuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
