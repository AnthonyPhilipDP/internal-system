<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Add New Employee')
            ->icon('heroicon-o-user-plus'),
        ];
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::SevenExtraLarge;
    }
}
