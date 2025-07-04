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
        $stats = [];

        $userLevel = auth()->user()->level;

        // If the user is an admin (level 1), show the employee count stat
        if ($userLevel == 1) {
            $stats[] = Stat::make('Employees', User::where('level', 2)->count());
        }

        $stats[] = Stat::make('Clients', Customer::count());
        $stats[] = Stat::make('Total Equipments', Equipment::count());
        $stats[] = Stat::make('Pending Equipments', Equipment::where('status', 'pending')->count());
        $stats[] = Stat::make('Incoming Equipments', Equipment::where('status', 'incoming')->count());
        $stats[] = Stat::make('Repair Equipments', Equipment::where('status', 'repair')->count());
        $stats[] = Stat::make('Last Acknowledgment Receipt ID', Equipment::whereNotNull('ar_id')->latest('created_at')->value('ar_id'));

        return $stats;

    }
}
