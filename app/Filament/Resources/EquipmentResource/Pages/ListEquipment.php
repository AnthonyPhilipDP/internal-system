<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\EquipmentResource;

class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('acknowledgmentReceipt')
                ->label('Acknowledgment Receipt')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Latest Acknowledgment Receipt')
                ->modalDescription('Please provide the name of the delivery person')
                ->modalSubmitActionLabel('Confirm')
                ->modalIcon('heroicon-o-arrow-left-end-on-rectangle')
                ->form([
                    TextInput::make('name')
                        ->label('Delivery Person Name')
                        ->autocomplete(false)
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Handle the action with the input data
                    $deliveryRider = $data['name'];
                    // Store the delivery rider's name in the session
                    Session::put('name', $deliveryRider);
                    // Redirect to the acknowledgment receipt page
                    return redirect()->to('/acknowledgment-receipt');
                }),
            CreateAction::make()
                ->label('Add New Equipment'),
        ];
    }
}
