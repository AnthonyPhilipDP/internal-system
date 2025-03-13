<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions;
use App\Models\Equipment;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
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
                ->modalHeading('Acknowledgment Receipt')
                ->modalDescription('Please provide the name of the delivery person')
                ->modalSubmitActionLabel('Confirm')
                ->modalIcon('heroicon-o-arrow-left-end-on-rectangle')
                ->form([
                    TextInput::make('name')
                        ->label('Delivery Person Name')
                        ->autocomplete(false)
                        ->required(),
                    Select::make('ar_id')
                        ->label('Receipt Number')
                        ->searchable()
                        ->preload()
                        ->optionsLimit(10)
                        ->searchDebounce(500)
                        ->searchPrompt('Search for your desired DR number')
                        ->searchingMessage('Searching DR number, please wait ...')
                        ->noSearchResultsMessage('No DR number found.')
                        ->getSearchResultsUsing(function (string $search) {
                            // Perform the search strictly on ar_id
                            return Equipment::where('ar_id', 'like', "%{$search}%")
                                ->pluck('ar_id')
                                ->mapWithKeys(function ($arId) {
                                    return [$arId => '401-' . $arId];
                                });
                        })
                        ->getOptionLabelUsing(function ($value) {
                            // Display the ar_id with the "401-" prefix
                            return '401-' . $value;
                        })
                        ->options(Equipment::pluck('ar_id')->unique()->mapWithKeys(function ($arId) {
                            return [$arId => '401-' . $arId];
                        }))
                        ->default(Equipment::max('ar_id'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Handle the action with the input data
                    $deliveryRider = $data['name'];
                    $arId = $data['ar_id'];
                    // Store the delivery rider's name and selected ar_id in the session
                    Session::put('name', $deliveryRider);
                    Session::put('ar_id', $arId);
                    // Redirect to the acknowledgment receipt page
                    return redirect()->to('/acknowledgment-receipt');
                }),
            CreateAction::make()
                ->label('Add New Equipment'),
        ];
    }
}