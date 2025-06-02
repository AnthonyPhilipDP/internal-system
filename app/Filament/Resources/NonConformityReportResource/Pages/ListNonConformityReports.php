<?php

namespace App\Filament\Resources\NonConformityReportResource\Pages;

use App\Filament\Resources\NonConformityReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNonConformityReports extends ListRecords
{
    protected static string $resource = NonConformityReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
