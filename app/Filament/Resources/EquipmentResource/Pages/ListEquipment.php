<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use Zxing\QrReader;
use Filament\Actions;
use App\Models\Equipment;
use Filament\Actions\Action;
use App\Models\DeliveryPerson;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\EquipmentResource;

class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('qrScanner')
                ->label('QR Code Scanner')
                ->color('info')
                ->icon('heroicon-o-qr-code')
                ->modalSubmitActionLabel('Go to equipment')
                ->form([
                Section::make('')
                    ->description('Upload a QR code to scan and view the equipment details')
                    ->schema([
                        FileUpload::make('qr_code')
                            ->label('Upload QR Code')
                            ->image()
                            ->directory('temp')
                            ->fetchFileInformation(false)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $qrReader = new \Zxing\QrReader($state->getRealPath());
                                    $decodedText = $qrReader->text();
                                    $equipment = \App\Models\Equipment::find($decodedText);
        
                                    if ($equipment) {
                                        $set('equipment_id', $equipment->equipment_id);
                                        $set('id', $equipment->id);
                                    } else {
                                        $set('equipment_id', 'Equipment not found.');
                                        $set('id', null);
                                    }
                                }
                            })
                            ->columnSpan(2),
                        TextInput::make('equipment_id')
                            ->label('Equipment ID')
                            ->readonly(),
                        TextInput::make('id')
                            ->label('Transaction ID')
                            ->readonly(),
                    ])->columns(2)
                ])
                ->action(function (array $data) {
                    if ($data['id']) {
                        return redirect()->to('/admin/equipment/' . $data['id'] . '/edit');
                    }
                }),
            Action::make('acknowledgmentReceipt')
                ->label('Acknowledgment Receipt')
                ->color('info')
                ->icon('heroicon-o-printer')
                ->requiresConfirmation()
                ->modalHeading('Acknowledgment Receipt')
                ->modalDescription('The acknowledgment receipt process is now automated to enhance efficiency and accuracy. Simply confirm the details to proceed seamlessly')
                ->modalSubmitActionLabel('Confirm')
                ->modalIcon('heroicon-o-printer')
                ->form([
                    TextInput::make('name')
                        ->label('Delivery Person Name')
                        ->placeholder('Please, enter the name of the delivery person')
                        ->autocomplete(false)
                        ->required()
                        ->reactive()
                        ->disabled(function (callable $get) {
                            $selectedArId = $get('ar_id') ?? Equipment::max('ar_id');
                            $deliveryPerson = DeliveryPerson::where('ar_id', $selectedArId)->first();
                            $toggleVisible = $deliveryPerson && !empty($deliveryPerson->name);
                            return $toggleVisible ? !$get('edit_name') : false;
                        })
                        ->dehydrated()
                        ->afterStateHydrated(function ($state, callable $get, callable $set) {
                            $arId = $get('ar_id') ?? Equipment::max('ar_id');
                            if ($arId) {
                                $deliveryPerson = DeliveryPerson::where('ar_id', $arId)->first();
                                $set('name', $deliveryPerson ? $deliveryPerson->name : '');
                            }
                        }),
                    Toggle::make('edit_name')
                        ->label('Enable Editing')
                        ->default(false)
                        ->reactive()
                        ->helperText('Toggle this button to edit the delivery person\'s name.')
                        ->visible(function (callable $get) {
                            $selectedArId = $get('ar_id') ?? Equipment::max('ar_id');
                            $deliveryPerson = DeliveryPerson::where('ar_id', $selectedArId)->first();
                            return $deliveryPerson && !empty($deliveryPerson->name);
                        })
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            if (!$state) { // If the toggle is turned off
                                $arId = $get('ar_id') ?? Equipment::max('ar_id');
                                if ($arId) {
                                    $deliveryPerson = DeliveryPerson::where('ar_id', $arId)->first();
                                    $set('name', $deliveryPerson ? $deliveryPerson->name : '');
                                }
                            }
                        }),
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
                            return Equipment::where('ar_id', 'like', "%{$search}%")
                                ->pluck('ar_id')
                                ->mapWithKeys(function ($arId) {
                                    return [$arId => '401-' . $arId];
                                });
                        })
                        ->getOptionLabelUsing(function ($value) {
                            return '401-' . $value;
                        })
                        ->options(Equipment::pluck('ar_id')->unique()->mapWithKeys(function ($arId) {
                            return [$arId => '401-' . $arId];
                        }))
                        ->default(Equipment::max('ar_id'))
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $deliveryPerson = DeliveryPerson::where('ar_id', $state)->first();
                                $set('name', $deliveryPerson ? $deliveryPerson->name : '');
                            } else {
                                $set('name', null); // Clear the name if no ar_id is selected
                            }
                        }),
                ])
                ->action(function (array $data) {
                    // Store the delivery person's name and ar_id in the delivery_people table
                    $this->saveDeliveryPerson($data);

                    // Store the delivery rider's name and selected ar_id in the session
                    Session::put('name', $data['name']);
                    Session::put('ar_id', $data['ar_id']);

                    // Redirect to the acknowledgment receipt page
                    return redirect()->to('/acknowledgment-receipt');
                }),
            CreateAction::make()
                ->label('Add New Equipment'),
        ];
    }

    public function saveDeliveryPerson($data)
    {
        // Check if a DeliveryPerson with the given ar_id already exists
        $deliveryPerson = DeliveryPerson::where('ar_id', $data['ar_id'])->first();

        if ($deliveryPerson) {
            // Update the existing DeliveryPerson's name
            $deliveryPerson->update(['name' => $data['name']]);
        } else {
            // Create a new DeliveryPerson record
            DeliveryPerson::create([
                'name' => $data['name'],
                'ar_id' => $data['ar_id'],
            ]);
        }
    }
}

