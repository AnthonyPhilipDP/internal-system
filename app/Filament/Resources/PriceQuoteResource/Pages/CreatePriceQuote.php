<?php

namespace App\Filament\Resources\PriceQuoteResource\Pages;

use App\Filament\Resources\PriceQuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePriceQuote extends CreateRecord
{
    protected static string $resource = PriceQuoteResource::class;
}
