<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Customer;
use App\Models\Equipment;
use Filament\Forms\Components\Actions\Action;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '2s';

    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected function getHeading(): ?string
    {
        return 'PMSi Analytics Overview';
    }
    
    protected function getDescription(): ?string
    {
        return 'Shows an accurate insights of the system';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Employees', User::where('level', 2)->count()),
            Stat::make('Clients', Customer::count()),
            Stat::make('Total Equipments', Equipment::count()),
            Stat::make('Pending Equipments', Equipment::where('status', 'pending')->count()),
            Stat::make('Last Acknowledgment Receipt ID', Equipment::whereNotNull('ar_id')->latest('created_at')->value('ar_id')),
        ];
    }
}
