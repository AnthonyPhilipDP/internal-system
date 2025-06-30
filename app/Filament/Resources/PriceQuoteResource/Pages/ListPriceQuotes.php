<?php

namespace App\Filament\Resources\PriceQuoteResource\Pages;

use App\Filament\Resources\PriceQuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPriceQuotes extends ListRecords
{
    protected static string $resource = PriceQuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('bi-file-earmark-spreadsheet'),
        ];
    }
}
