<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Models\Equipment;
use Filament\Tables\Table;
use App\Models\ContactPerson;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\InvoiceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InvoiceResource\RelationManagers;

class InvoiceResource extends Resource
{
    protected static ?string $navigationGroup = 'PMSi';
    
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'bi-envelope-paper';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Components\Fieldset::make('Customer Information')
                ->extraAttributes(['class' => 'bg-red-50'])
                ->schema([
                    Components\TextInput::make('customer_id')
                        ->label('Customer')
                        ->disabled()
                        ->formatStateUsing(function ($state) {
                            $customer = Customer::where('customer_id', $state)->first();
                            return $customer ? $customer->name : 'Unknown';
                        })
                        ->reactive(),
                    Components\Select::make('contactPerson')
                        ->label('Attention To')
                        ->native(false)
                        ->required()
                        ->options(function (callable $get) {
                            $customerId = $get('customer_id');
                            return $customerId
                                ? ContactPerson::where('customer_id', $customerId)->latest('created_at')->pluck('name', 'name')->toArray()
                                : [];
                        })
                        ->createOptionForm([
                            Components\Fieldset::make('Add a new contact person for this customer')
                                ->schema([
                                    Components\Select::make('identity')
                                        ->label('Identify As')
                                        ->options(['male' => 'Mr', 'female' => 'Ms'])
                                        ->native(false)
                                        ->required(),
                                    Components\TextInput::make('name')->label('Contact Name')->required(),
                                    Components\TextInput::make('contact1')->label('Primary Contact Number')->length(11)->tel()->required(),
                                    Components\TextInput::make('contact2')->label('Secondary Contact Number'),
                                    Components\TextInput::make('department')->label('Department'),
                                    Components\TextInput::make('position')->label('Position'),
                                    Components\TextInput::make('email')->label('Email')->email(),
                                    Components\Toggle::make('isActive')->label('Active Status')->default(true)->inline(),
                                ])->columns(4)
                        ])
                        ->createOptionUsing(function (array $data, callable $get) {
                            $customerId = $get('customer_id');
                            $contactPerson = ContactPerson::create([
                                'customer_id' => $customerId,
                                'identity' => $data['identity'],
                                'name' => $data['name'],
                                'contact1' => $data['contact1'],
                                'contact2' => $data['contact2'],
                                'department' => $data['department'],
                                'position' => $data['position'],
                                'email' => $data['email'],
                            ]);
                            return $contactPerson->getKey();
                        }),
                    Components\TextInput::make('carbonCopy')->label('CC'),
                    Components\TextInput::make('invoice_number')
                        ->label('Sales Invoice')
                        ->prefix('83-00-')
                        ->default(fn () => (Invoice::max('invoice_number') ?? 4337) + 1)
                        ->disabled(),
                    Components\DatePicker::make('invoice_date')->label('Invoice Date')->required()->default(now()),
                    Components\TextInput::make('poNoCalibration')->label('Your PO'),
                    Components\TextInput::make('yourRef')->label('Your Ref'),
                    Components\TextInput::make('pmsiRefNo')->label('PMSi Ref #'),
                    Components\TextInput::make('freeOnBoard')->label('FOB'),
                    Components\TextInput::make('businessSystem')
                        ->label('Business System')
                        ->default(fn (callable $get) => optional(Customer::find($get('customer_id')))->businessStyle),
                    Components\TextInput::make('tin')
                        ->label('TIN #')
                        ->default(fn (callable $get) => optional(Customer::find($get('customer_id')))->tin),
                    Components\Select::make('service')
                        ->label('Service')
                        ->required()
                        ->native(false)
                        ->options([
                            'calibration' => 'Calibration',
                            'repair' => 'Repair',
                            'realignment' => 'Re-alignment',
                            'cal_repair' => 'Cal / Repair',
                            'cal_realign' => 'Cal / Re-align',
                            'repair_realign' => 'Repair / Realign',
                            'cal_repair_realign' => 'Cal / Repair / Realign',
                        ]),
                    Components\Select::make('payment')
                        ->label('Payment')
                        ->native(false)
                        ->required()
                        ->options(fn (callable $get) => $get('payment_options') ?? ['cod' => 'Cash On Delivery'])
                        ->createOptionForm([
                            Components\TextInput::make('new_payment_method')->label('Add Another Payment Method')->required(),
                        ])
                        ->createOptionUsing(function (array $data, callable $set, callable $get) {
                            $newPaymentMethod = $data['new_payment_method'];
                            $currentOptions = $get('payment_options') ?? [];
                            $currentOptions[$newPaymentMethod] = $newPaymentMethod;
                            $set('payment_options', $currentOptions);
                            return $newPaymentMethod;
                        })
                        ->reactive()
                        ->afterStateHydrated(function (callable $set, callable $get) {
                            if (!$get('payment_options')) {
                                $set('payment_options', ['cod' => 'Cash On Delivery']);
                            }
                        }),
                ])->columns(4),
    
            // Items
            Components\Repeater::make('items')
                ->relationship()
                ->label('Invoice Items')
                ->schema([
                    Components\Fieldset::make(fn () => "")
                    ->extraAttributes([
                        'class' => 'bg-blue-50'
                    ])
                    ->schema([
                        Components\Grid::make(5)
                        ->schema([
                            // First Grid
                            Components\Group::make([
                                Components\Fieldset::make('')
                                    ->extraAttributes([
                                        'class' => 'bg-orange-50'
                                    ])
                                    ->schema([
                                        Components\Grid::make(8)
                                        ->schema([
                                        Components\TextInput::make("item_number")
                                            ->label('Item')
                                            ->extraInputAttributes([
                                                'class' => 'text-center'
                                            ])
                                            ->columnSpan(1)
                                            ->disabled()
                                            ->dehydrated(),
                                        Components\TextInput::make("equipment_id")
                                            ->label('Equipment ID')
                                            // ->formatStateUsing(function ($record) {
                                            //     $transaction_id = $record->transaction_id;
                                            //     $id = Equipment::where('transaction_id', $transaction_id)->first();
                                            //     return $id ? $id->equipment_id : 'Unknown';
                                            // })
                                            ->columnSpan(2)
                                            ->disabled(),
                                        Components\TextInput::make("service")
                                            ->label('Service')
                                            ->columnSpan(3)
                                            // ->formatStateUsing(function ($record) {
                                            //     $services = [
                                            //         'calibration' => 'Calibration',
                                            //         'cal and realign' => 'Calibration and Realign',
                                            //         'cal and repair' => 'Calibration and Repair',
                                            //         'repair' => 'Repair',
                                            //         'diagnostic' => 'Diagnostic',
                                            //         'N/A' => 'Not Available',
                                            //     ];
                                        
                                            //     $transaction_id = $record->transaction_id;
                                            //     $equipment = Equipment::where('transaction_id', $transaction_id)->first();
                                        
                                            //     if ($equipment && isset($services[$equipment->service])) {
                                            //         return $services[$equipment->service];
                                            //     }
                                        
                                            //     return 'N/A';
                                            // })
                                            ->disabled(),
                                        Components\TextInput::make("status")
                                            ->label('Status')
                                            ->columnSpan(2)
                                            // ->formatStateUsing(function ($record) {
                                            //     $statuses = [
                                            //         'completed' => 'Completed',
                                            //         'delivered' => 'Delivered',
                                            //         'picked-up' => 'Picked-up',
                                            //         'repair' => 'Repair',
                                            //         'pending' => 'Pending',
                                            //         'on-hold' => 'On-hold',
                                            //         'incoming' => 'Incoming',
                                            //         'returned' => 'Returned',
                                            //         'on-site' => 'On-site',
                                            //         'for sale' => 'For sale',
                                            //     ];
                                        
                                            //     $transaction_id = $record->transaction_id;
                                            //     $equipment = Equipment::where('transaction_id', $transaction_id)->first();
                                        
                                            //     if ($equipment && isset($statuses[$equipment->status])) {
                                            //         return $statuses[$equipment->status];
                                            //     }
                                        
                                            //     return 'N/A';
                                            // })
                                            ->disabled(),
                                        Components\TextInput::make("make")
                                            ->label('Make')
                                            // ->formatStateUsing(function ($record) {
                                            //     $transaction_id = $record->transaction_id;
                                            //     $id = Equipment::where('transaction_id', $transaction_id)->first();
                                            //     return $id ? $id->make : 'Unknown';
                                            // })
                                            ->columnSpan(2)
                                            ->disabled(),
                                        Components\TextInput::make("model")
                                            ->label('Model')
                                            // ->formatStateUsing(function ($record) {
                                            //     $transaction_id = $record->transaction_id;
                                            //     $id = Equipment::where('transaction_id', $transaction_id)->first();
                                            //     return $id ? $id->model : 'Unknown';
                                            // })
                                            ->columnSpan(2)
                                            ->disabled(),
                                        Components\TextInput::make("description")
                                            ->label('Description')
                                            // ->formatStateUsing(function ($record) {
                                            //     $transaction_id = $record->transaction_id;
                                            //     $id = Equipment::where('transaction_id', $transaction_id)->first();
                                            //     return $id ? $id->description : 'Unknown';
                                            // })
                                            ->columnSpan(2)
                                            ->disabled(),
                                        Components\TextInput::make("serial")
                                            ->label('Serial')
                                            // ->formatStateUsing(function ($record) {
                                            //     $transaction_id = $record->transaction_id;
                                            //     $id = Equipment::where('transaction_id', $transaction_id)->first();
                                            //     return $id ? $id->serial : 'Unknown';
                                            // })
                                            ->columnSpan(2)
                                            ->disabled(),
                                        Components\TextInput::make("transaction_id")
                                            ->label('Transaction ID')
                                            ->columnSpan(2)
                                            ->disabled()
                                            ->dehydrated(),
                                        Components\TextInput::make("code_range")
                                            ->label('Code | Range')
                                            // ->formatStateUsing(function ($record) {
                                            //     $transaction_id = $record->transaction_id;
                                            //     $id = Equipment::where('transaction_id', $transaction_id)->first();
                                            //     return $id ? $id->code_range : 'Unknown';
                                            // })
                                            ->columnSpan(6)
                                            ->disabled(),
                                        ])
                                    ])
                            ])->columnSpan('2'),

                            // Middle Grid
                            Components\Group::make([ // You are here Friday June 20, 2025
                                Components\Fieldset::make('')
                                    ->extraAttributes([
                                        'class' => 'bg-orange-50'
                                    ])
                                    ->schema([
                                        Components\Grid::make(1)
                                        ->schema([
                                            Components\TextInput::make("quantity")
                                            ->label('Quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $quantity = (float) $state;
                                                $baseUnitPrice = (float) ($get("unit_price") ?? 0);
                                                $vatOnEquipment = $get('vatToggle');
                                                $unitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                $subTotal = $quantity * $unitPrice;
                                                $set("equipment_subtotal", number_format($subTotal, 2, '.', ''));
                                        
                                                $baseSubTotal = $baseUnitPrice * $quantity;
                                                $lessPercentage = (float) ($get("less_percentage") ?? 0);
                                                $lessAmount = $baseSubTotal * ($lessPercentage / 100);
                                                $set("less_amount", number_format($lessAmount, 2, '.', ''));
                                        
                                                $chargePercentage = (float) ($get("charge_percentage") ?? 0);
                                                $chargeAmount = $baseSubTotal * ($chargePercentage / 100);
                                                $set("charge_amount", number_format($chargeAmount, 2, '.', ''));
                                        
                                                $lineTotal = $subTotal - $lessAmount + $chargeAmount;
                                                $set("line_total", number_format($lineTotal, 2, '.', ''));
                                        
                                                // --- Summary calculation for all items ---
                                                $overallSub = 0;
                                                $totalChargeAmount = 0;
                                                $totalLessAmount = 0;
                                                $items = $get('../../items') ?? [];
                                                foreach ($items as $item) {
                                                    $overallSub += (float) ($item['equipment_subtotal'] ?? 0);
                                                    $totalChargeAmount += (float) ($item['charge_amount'] ?? 0);
                                                    $totalLessAmount += (float) ($item['less_amount'] ?? 0);
                                                }
                                                $set('../../subTotal', number_format($overallSub, 2, '.', ''));
                                                $set('../../vatAmount', $get('../../vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));
                                                $set('../../global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                $set('../../global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                                        
                                                $total = $overallSub
                                                    + ($get('../../vatToggle') ? 0 : ($overallSub * 0.12))
                                                    + $totalChargeAmount
                                                    - $totalLessAmount;
                                                $set('../../total', number_format($total, 2, '.', ''));
                                            }),
                                        Components\TextInput::make("unit_price")
                                            ->label('Unit Price')
                                            ->numeric()
                                            ->default('0')
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $baseUnitPrice = (float) $state;
                                                $vatOnEquipment = $get('vatToggle');
                                                $unitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                $quantity = (float) ($get("quantity") ?? 0);
                                                $subTotal = $quantity * $unitPrice;
                                                $set("equipment_subtotal", number_format($subTotal, 2, '.', ''));
                                        
                                                $baseSubTotal = $baseUnitPrice * $quantity;
                                                $lessPercentage = (float) ($get("less_percentage") ?? 0);
                                                $lessAmount = $baseSubTotal * ($lessPercentage / 100);
                                                $set("less_amount", number_format($lessAmount, 2, '.', ''));
                                        
                                                $chargePercentage = (float) ($get("charge_percentage") ?? 0);
                                                $chargeAmount = $baseSubTotal * ($chargePercentage / 100);
                                                $set("charge_amount", number_format($chargeAmount, 2, '.', ''));
                                        
                                                $lineTotal = $subTotal - $lessAmount + $chargeAmount;
                                                $set("line_total", number_format($lineTotal, 2, '.', ''));
                                        
                                                // --- Summary calculation for all items ---
                                                $overallSub = 0;
                                                $totalChargeAmount = 0;
                                                $totalLessAmount = 0;
                                                $items = $get('../../items') ?? [];
                                                foreach ($items as $item) {
                                                    $overallSub += (float) ($item['equipment_subtotal'] ?? 0);
                                                    $totalChargeAmount += (float) ($item['charge_amount'] ?? 0);
                                                    $totalLessAmount += (float) ($item['less_amount'] ?? 0);
                                                }
                                                $set('../../subTotal', number_format($overallSub, 2, '.', ''));
                                                $set('../../vatAmount', $get('../../vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));
                                                $set('../../global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                $set('../../global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                                        
                                                $total = $overallSub
                                                    + ($get('../../vatToggle') ? 0 : ($overallSub * 0.12))
                                                    + $totalChargeAmount
                                                    - $totalLessAmount;
                                                $set('../../total', number_format($total, 2, '.', ''));
                                            }),
                                        Components\TextInput::make("equipment_subtotal")
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->default('0.00')
                                            ->readOnly(),
                                        ])
                                    ])
                            ])->columnSpan('1'),

                            // Last Grid
                            Components\Group::make([
                                Components\Fieldset::make('')
                                    ->extraAttributes([
                                        'class' => 'bg-orange-50'
                                    ])
                                    ->schema([
                                        Components\Grid::make(4)
                                        ->schema([
                                        Components\Select::make("less_type")
                                            ->label('Less Type')
                                            ->columnSpan(2)
                                            ->native(false)
                                            ->options([
                                                'discount' => 'Discount',
                                                'other' => 'Other'
                                            ])
                                            ->createOptionForm([
                                                Components\TextInput::make('new_less_type')
                                                    ->label('Add Another Less Type')
                                                    ->required(),
                                            ])
                                            ->createOptionUsing(function (array $data, callable $set, callable $get): string {
                                                $newLessType = $data['new_less_type'];
                                                $currentOptions = $get('less_type_options') ?? [];
                                                $currentOptions[$newLessType] = $newLessType;
                                                $set('less_type_options', $currentOptions);
                                                return $newLessType;
                                            })
                                            ->reactive()
                                            ->afterStateHydrated(function (callable $set, callable $get) {
                                                if (!$get('less_type_options')) {
                                                    $set('less_type_options', [
                                                        'discount' => 'Discount',
                                                        'other' => 'Other'
                                                    ]);
                                                }
                                            })
                                            ->options(function (callable $get) {
                                                return $get('less_type_options');
                                            }),
                                        Components\TextInput::make("less_percentage")
                                            ->label('Less (%)')
                                            ->columnSpan(1)
                                            ->numeric()
                                            ->default('0.00')
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $baseUnitPrice = (float) ($get("unit_price") ?? 0);
                                                $quantity = (float) ($get("quantity") ?? 0);
                                                $baseSubTotal = $baseUnitPrice * $quantity;
                                        
                                                // Calculate less_amount from percentage and set it
                                                $lessAmount = $baseSubTotal * ((float) $state / 100);
                                                $set("less_amount", number_format($lessAmount, 2, '.', ''));
                                        
                                                // Now recalculate line total and summary using this less_amount
                                                $chargeAmount = (float) ($get("charge_amount") ?? 0);
                                                $vatOnEquipment = $get('vatToggle');
                                                $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                $displaySubTotal = $quantity * $displayUnitPrice;
                                                $lineTotal = $displaySubTotal + $chargeAmount - $lessAmount;
                                                $set("line_total", number_format($lineTotal, 2, '.', ''));
                                        
                                                // --- Summary calculation ---
                                                $overallSub = 0;
                                                $totalChargeAmount = 0;
                                                $totalLessAmount = 0;
                                                $items = $get('../../items') ?? [];
                                                foreach ($items as $item) {
                                                    $overallSub += (float) ($item['equipment_subtotal'] ?? 0);
                                                    $totalChargeAmount += (float) ($item['charge_amount'] ?? 0);
                                                    $totalLessAmount += (float) ($item['less_amount'] ?? 0);
                                                }
                                                $set('../../subTotal', number_format($overallSub, 2, '.', ''));
                                                $set('../../vatAmount', $get('../../vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));
                                                $set('../../global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                $set('../../global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                                        
                                                $total = $overallSub
                                                    + ($get('../../vatToggle') ? 0 : ($overallSub * 0.12))
                                                    + $totalChargeAmount
                                                    - $totalLessAmount;
                                                $set('../../total', number_format($total, 2, '.', ''));
                                            }),
                                        Components\TextInput::make("less_amount")
                                            ->label('Less Amount')
                                            ->columnSpan(1)
                                            ->numeric()
                                            ->default('0.00')
                                            ->live(debounce: 500)
                                            ->dehydrated()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $lessAmount = (float) $state;
                                        
                                                // Use this lessAmount for calculations
                                                $chargeAmount = (float) ($get("charge_amount") ?? 0);
                                                $baseUnitPrice = (float) ($get("unit_price") ?? 0);
                                                $quantity = (float) ($get("quantity") ?? 0);
                                                $vatOnEquipment = $get('vatToggle');
                                                $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                $displaySubTotal = $quantity * $displayUnitPrice;
                                                $lineTotal = $displaySubTotal + $chargeAmount - $lessAmount;
                                                $set("line_total", number_format($lineTotal, 2, '.', ''));
                                        
                                                // --- Summary calculation ---
                                                $overallSub = 0;
                                                $totalChargeAmount = 0;
                                                $totalLessAmount = 0;
                                                $items = $get('../../items') ?? [];
                                                foreach ($items as $item) {
                                                    $overallSub += (float) ($item['equipment_subtotal'] ?? 0);
                                                    $totalChargeAmount += (float) ($item['charge_amount'] ?? 0);
                                                    $totalLessAmount += (float) ($item['less_amount'] ?? 0);
                                                }
                                                $set('../../subTotal', number_format($overallSub, 2, '.', ''));
                                                $set('../../vatAmount', $get('../../vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));
                                                $set('../../global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                $set('../../global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                                        
                                                $total = $overallSub
                                                    + ($get('../../vatToggle') ? 0 : ($overallSub * 0.12))
                                                    + $totalChargeAmount
                                                    - $totalLessAmount;
                                                $set('../../total', number_format($total, 2, '.', ''));
                                            }),
                                        Components\Select::make("charge_type")
                                            ->label('Charge Type')
                                            ->columnSpan(2)
                                            ->native(false)
                                            ->options([
                                                'On-site fee' => 'On-site fee',
                                                'Delivery Fee' => 'Delivery Fee',
                                                'Expedite Fee' => 'Expedite Fee',
                                                'other' => 'Other'
                                            ])
                                            ->createOptionForm([
                                                Components\TextInput::make('new_charge_type')
                                                    ->label('Add Another Charge Type')
                                                    ->required(),
                                            ])
                                            ->createOptionUsing(function (array $data, callable $set, callable $get): string {
                                                $newChargeType = $data['new_charge_type'];
                                                $currentOptions = $get('charge_type_options') ?? [];
                                                $currentOptions[$newChargeType] = $newChargeType;
                                                $set('charge_type_options', $currentOptions);
                                                return $newChargeType;
                                            })
                                            ->reactive()
                                            ->afterStateHydrated(function (callable $set, callable $get) {
                                                if (!$get('charge_type_options')) {
                                                    $set('charge_type_options', [
                                                        'On-site fee' => 'On-site fee',
                                                        'Delivery Fee' => 'Delivery Fee',
                                                        'Expedite Fee' => 'Expedite Fee',
                                                        'other' => 'Other'
                                                    ]);
                                                }
                                            })
                                            ->options(function (callable $get) {
                                                return $get('charge_type_options');
                                            }),
                                        Components\TextInput::make("charge_percentage")
                                            ->label('Charge (%)')
                                            ->columnSpan(1)
                                            ->numeric()
                                            ->default('0.00')
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $baseUnitPrice = (float) ($get("unit_price") ?? 0);
                                                $quantity = (float) ($get("quantity") ?? 0);
                                                $baseSubTotal = $baseUnitPrice * $quantity;
                                        
                                                // Calculate charge_amount from percentage and set it
                                                $chargeAmount = $baseSubTotal * ((float) $state / 100);
                                                $set("charge_amount", number_format($chargeAmount, 2, '.', ''));
                                        
                                                // Now recalculate line total and summary using this charge_amount
                                                $lessAmount = (float) ($get("less_amount") ?? 0);
                                                $vatOnEquipment = $get('vatToggle');
                                                $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                $displaySubTotal = $quantity * $displayUnitPrice;
                                                $lineTotal = $displaySubTotal + $chargeAmount - $lessAmount;
                                                $set("line_total", number_format($lineTotal, 2, '.', ''));
                                        
                                                // --- Summary calculation ---
                                                $overallSub = 0;
                                                $totalChargeAmount = 0;
                                                $totalLessAmount = 0;
                                                $items = $get('../../items') ?? [];
                                                foreach ($items as $item) {
                                                    $overallSub += (float) ($item['equipment_subtotal'] ?? 0);
                                                    $totalChargeAmount += (float) ($item['charge_amount'] ?? 0);
                                                    $totalLessAmount += (float) ($item['less_amount'] ?? 0);
                                                }
                                                $set('../../subTotal', number_format($overallSub, 2, '.', ''));
                                                $set('../../vatAmount', $get('../../vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));
                                                $set('../../global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                $set('../../global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                                        
                                                $total = $overallSub
                                                    + ($get('../../vatToggle') ? 0 : ($overallSub * 0.12))
                                                    + $totalChargeAmount
                                                    - $totalLessAmount;
                                                $set('../../total', number_format($total, 2, '.', ''));
                                            }),
                                        Components\TextInput::make("charge_amount")
                                            ->label('Charge Amount')
                                            ->columnSpan(1)
                                            ->numeric()
                                            ->default('0.00')
                                            ->live(debounce: 500)
                                            ->dehydrated()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $chargeAmount = (float) $state;
                                        
                                                // Use this chargeAmount for calculations
                                                $lessAmount = (float) ($get("less_amount") ?? 0);
                                                $baseUnitPrice = (float) ($get("unit_price") ?? 0);
                                                $quantity = (float) ($get("quantity") ?? 0);
                                                $vatOnEquipment = $get('vatToggle');
                                                $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                $displaySubTotal = $quantity * $displayUnitPrice;
                                                $lineTotal = $displaySubTotal + $chargeAmount - $lessAmount;
                                                $set("line_total", number_format($lineTotal, 2, '.', ''));
                                        
                                                // --- Summary calculation ---
                                                $overallSub = 0;
                                                $totalChargeAmount = 0;
                                                $totalLessAmount = 0;
                                                $items = $get('../../items') ?? [];
                                                foreach ($items as $item) {
                                                    $overallSub += (float) ($item['equipment_subtotal'] ?? 0);
                                                    $totalChargeAmount += (float) ($item['charge_amount'] ?? 0);
                                                    $totalLessAmount += (float) ($item['less_amount'] ?? 0);
                                                }
                                                $set('../../subTotal', number_format($overallSub, 2, '.', ''));
                                                $set('../../vatAmount', $get('../../vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));
                                                $set('../../global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                $set('../../global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                                        
                                                $total = $overallSub
                                                    + ($get('../../vatToggle') ? 0 : ($overallSub * 0.12))
                                                    + $totalChargeAmount
                                                    - $totalLessAmount;
                                                $set('../../total', number_format($total, 2, '.', ''));
                                            }),
                                        Components\TextInput::make("line_total")
                                            ->label('Total')
                                            ->numeric()
                                            ->default('0.00')
                                            ->readOnly()
                                            ->columnSpan(4)
                                            ->extraInputAttributes([
                                                'class' => 'text-center'
                                            ]),
                                        ])
                                ])
                            ])->columnSpan('2'),
                            
                        ])
                    ])
                ])
                ->columnSpanFull()
                ->addable(false)
                ->deletable(false)
                ->createItemButtonLabel('Add Item'),
    
            // Invoice Computation
            Components\Fieldset::make('Invoice Computation')
                ->extraAttributes(['class' => 'bg-yellow-50'])
                ->schema([
                    Components\Grid::make(2)
                    ->schema([
                        Components\Toggle::make('applyToAll')
                            ->label('Apply less/charges to all equipment')
                            ->columnSpan(2)
                            ->reactive(),
                        Components\Group::make([
                            Components\Fieldset::make('Less')
                            ->schema([
                                Components\Select::make('global_less_type')
                                    ->columnSpan(2)
                                    ->native(false)
                                    ->label('Less Type')
                                    ->options([
                                        'discount' => 'Discount',
                                        'other' => 'Other'
                                    ])
                                    ->createOptionForm([
                                        Components\TextInput::make('new_less_type')
                                            ->label('Add Another Less Type')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data, callable $set, callable $get): string {
                                        $newLessType = $data['new_less_type'];
                                        $currentOptions = $get('global_less_type_options') ?? [];
                                        $currentOptions[$newLessType] = $newLessType;
                                        $set('global_less_type_options', $currentOptions);
                                        return $newLessType;
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($get('applyToAll')) {
                                            $items = $get('items') ?? [];
                                            foreach ($items as $index => $item) {
                                                $set("items.{$index}.less_type", $state);
                                            }
                                        }
                                    })
                                    ->options(function (callable $get) {
                                        return $get('global_less_type_options') ?? [
                                            'discount' => 'Discount',
                                            'other' => 'Other'
                                        ];
                                    }),

                                Components\TextInput::make('global_less_percentage')
                                    ->columnSpan(1)
                                    ->label('Less (%)')
                                    ->numeric()
                                    ->default('0.00')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $items = $get('items') ?? [];
                                        $subTotal = (float) ($get('subTotal') ?? 0);
                                
                                        // Update each item's less_amount and line_total to reflect the global less percentage
                                        foreach ($items as $index => $item) {
                                            $baseUnitPrice = (float) ($item['unit_price'] ?? 0);
                                            $quantity = (float) ($item['quantity'] ?? 0);
                                            $baseSubTotal = $baseUnitPrice * $quantity;
                                
                                            // Calculate less_amount for this item (proportional to its base subtotal)
                                            $itemLessAmount = $baseSubTotal * ((float) $state / 100);
                                            $set("items.{$index}.less_amount", number_format($itemLessAmount, 2, '.', ''));
                                
                                            $chargeAmount = (float) ($item['charge_amount'] ?? 0);
                                            $vatOnEquipment = $get('vatToggle');
                                            $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                            $displaySubTotal = $quantity * $displayUnitPrice;
                                
                                            $lineTotal = $displaySubTotal + $chargeAmount - $itemLessAmount;
                                            $set("items.{$index}.line_total", number_format($lineTotal, 2, '.', ''));
                                        }
                                
                                        // Update summary fields
                                        $vatAmount = (float) ($get('vatAmount') ?? 0);
                                        $globalChargeAmount = (float) ($get('global_charge_amount') ?? 0);
                                
                                        $totalLessAmount = 0;
                                        foreach ($items as $index => $item) {
                                            $totalLessAmount += (float) ($get("items.{$index}.less_amount") ?? 0);
                                        }
                                        $set('global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                                
                                        $total = $subTotal
                                            + $vatAmount
                                            + $globalChargeAmount
                                            - $totalLessAmount;
                                        $set('total', number_format($total, 2, '.', ''));
                                    }),

                                Components\TextInput::make('global_less_amount')
                                    ->columnSpan(1)
                                    ->label('Less Amount')
                                    ->numeric()
                                    ->default('0.00')
                                    ->reactive()
                                    ->readOnly()
                                    ->afterStateHydrated(function (callable $set, callable $get) {
                                        // On form load, sum all less_amounts
                                        $sum = 0;
                                        $items = $get('items') ?? [];
                                        foreach ($items as $item) {
                                            $sum += (float) ($item['less_amount'] ?? 0);
                                        }
                                        $set('global_less_amount', number_format($sum, 2, '.', ''));
                                    })
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // When user types, do nothing (or optionally sync to all, but you want sum)
                                        if ($get('applyToAll')) {
                                            $sum = 0;
                                            $items = $get('items') ?? [];
                                            foreach ($items as $item) {
                                                $sum += (float) ($item['less_amount'] ?? 0);
                                            }
                                            $set('global_less_amount', number_format($sum, 2, '.', ''));
                                        }
                                    }),
                                ])->columns(4)
                            ])->visible(fn (callable $get) => $get('applyToAll')),
                        Components\Group::make([
                            Components\Fieldset::make('Charges')
                            ->schema([
                                Components\Select::make('global_charge_type')
                                    ->columnSpan(2)
                                    ->native(false)
                                    ->label('Charge Type')
                                    ->options([
                                        'On-site fee' => 'On-site fee',
                                        'Delivery Fee' => 'Delivery Fee',
                                        'Expedite Fee' => 'Expedite Fee',
                                        'other' => 'Other'
                                    ])
                                    ->createOptionForm([
                                        Components\TextInput::make('new_charge_type')
                                            ->label('Add Another Charge Type')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data, callable $set, callable $get): string {
                                        $newChargeType = $data['new_charge_type'];
                                        $currentOptions = $get('global_charge_type_options') ?? [];
                                        $currentOptions[$newChargeType] = $newChargeType;
                                        $set('global_charge_type_options', $currentOptions);
                                        return $newChargeType;
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($get('applyToAll')) {
                                            $items = $get('items') ?? [];
                                            foreach ($items as $index => $item) {
                                                $set("items.{$index}.charge_type", $state);
                                            }
                                        }
                                    })
                                    ->options(function (callable $get) {
                                        return $get('global_charge_type_options') ?? [
                                            'On-site fee' => 'On-site fee',
                                            'Delivery Fee' => 'Delivery Fee',
                                            'Expedite Fee' => 'Expedite Fee',
                                            'other' => 'Other'
                                        ];
                                    }),

                                Components\TextInput::make('global_charge_percentage')
                                    ->columnSpan(1)
                                    ->label('Charge (%)')
                                    ->numeric()
                                    ->default('0.00')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $items = $get('items') ?? [];
                                        $subTotal = (float) ($get('subTotal') ?? 0);
                                
                                        // Update each item's charge_amount and line_total to reflect the global charge percentage
                                        foreach ($items as $index => $item) {
                                            $baseUnitPrice = (float) ($item['unit_price'] ?? 0);
                                            $quantity = (float) ($item['quantity'] ?? 0);
                                            $baseSubTotal = $baseUnitPrice * $quantity;
                                
                                            // Calculate charge_amount for this item (proportional to its base subtotal)
                                            $itemChargeAmount = $baseSubTotal * ((float) $state / 100);
                                            $set("items.{$index}.charge_amount", number_format($itemChargeAmount, 2, '.', ''));
                                
                                            $lessAmount = (float) ($item['less_amount'] ?? 0);
                                            $vatOnEquipment = $get('vatToggle');
                                            $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                            $displaySubTotal = $quantity * $displayUnitPrice;
                                
                                            $lineTotal = $displaySubTotal + $itemChargeAmount - $lessAmount;
                                            $set("items.{$index}.line_total", number_format($lineTotal, 2, '.', ''));
                                        }
                                
                                        // Update summary fields
                                        $vatAmount = (float) ($get('vatAmount') ?? 0);
                                        $globalLessAmount = (float) ($get('global_less_amount') ?? 0);
                                
                                        $totalChargeAmount = 0;
                                        foreach ($items as $index => $item) {
                                            $totalChargeAmount += (float) ($get("items.{$index}.charge_amount") ?? 0);
                                        }
                                        $set('global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                
                                        $total = $subTotal
                                            + $vatAmount
                                            + $totalChargeAmount
                                            - $globalLessAmount;
                                        $set('total', number_format($total, 2, '.', ''));
                                    }),

                                Components\TextInput::make('global_charge_amount')
                                    ->columnSpan(1)
                                    ->label('Charge Amount')
                                    ->numeric()
                                    ->default('0.00')
                                    ->reactive()
                                    ->readOnly()
                                    ->afterStateHydrated(function (callable $set, callable $get) {
                                        // On form load, sum all charge_amounts
                                        $sum = 0;
                                        $items = $get('items') ?? [];
                                        foreach ($items as $item) {
                                            $sum += (float) ($item['charge_amount'] ?? 0);
                                        }
                                        $set('global_charge_amount', number_format($sum, 2, '.', ''));
                                    })
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($get('applyToAll')) {
                                            $sum = 0;
                                            $items = $get('items') ?? [];
                                            foreach ($items as $item) {
                                                $sum += (float) ($item['charge_amount'] ?? 0);
                                            }
                                            $set('global_charge_amount', number_format($sum, 2, '.', ''));
                                        }
                                    }),
                            ])->columns(4)
                        ])->visible(fn (callable $get) => $get('applyToAll')),
                    ]),
                    Components\Grid::make(2)
                    ->schema([
                        Components\Toggle::make('applyEwt')
                            ->label('Apply EWT')
                            ->columnSpan(2)
                            ->reactive(),
                        Components\Group::make([
                            Components\Fieldset::make('Less')
                            ->schema([
                                Components\TextInput::make('ewt_percentage')
                                    ->columnSpan(1)
                                    ->label('Less (%)')
                                    ->numeric()
                                    ->default('0.00')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $subTotal = (float) ($get('subTotal') ?? 0);
                                        $ewtAmount = $subTotal * ((float) $state / 100);
                                        $set('ewt_amount', number_format($ewtAmount, 2, '.', ''));
                                
                                        // Update total
                                        $vatAmount = (float) ($get('vatAmount') ?? 0);
                                        $globalChargeAmount = (float) ($get('global_charge_amount') ?? 0);
                                        $globalLessAmount = (float) ($get('global_less_amount') ?? 0);
                                
                                        $total = $subTotal
                                            + $vatAmount
                                            + $globalChargeAmount
                                            - $globalLessAmount
                                            - $ewtAmount;
                                        $set('total', number_format($total, 2, '.', ''));
                                    }),

                                Components\TextInput::make('ewt_amount')
                                    ->columnSpan(1)
                                    ->label('Less Amount')
                                    ->numeric()
                                    ->default('0.00')
                                    ->reactive()
                                    ->afterStateHydrated(function (callable $set, callable $get) {
                                        $subTotal = (float) ($get('subTotal') ?? 0);
                                        $ewtPercentage = (float) ($get('ewt_percentage') ?? 0);
                                        $ewtAmount = $subTotal * ($ewtPercentage / 100);
                                        $set('ewt_amount', number_format($ewtAmount, 2, '.', ''));
                                    })
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // When EWT amount is changed directly, update the total
                                        $ewtAmount = (float) $state;
                                        $subTotal = (float) ($get('subTotal') ?? 0);
                                        $vatAmount = (float) ($get('vatAmount') ?? 0);
                                        $globalChargeAmount = (float) ($get('global_charge_amount') ?? 0);
                                        $globalLessAmount = (float) ($get('global_less_amount') ?? 0);
                                
                                        $total = $subTotal
                                            + $vatAmount
                                            + $globalChargeAmount
                                            - $globalLessAmount
                                            - $ewtAmount;
                                        $set('total', number_format($total, 2, '.', ''));
                                    }),
                                ])->columns(4)
                            ])->visible(fn (callable $get) => $get('applyEwt')),
                    ]),
                    Components\Textarea::make('comments')
                        ->label('Comments')
                        ->rows(1)
                        ->autosize()
                        ->columnSpan(3)
                        ->placeholder('Enter any additional notes for the invoice'),
                    Components\Select::make('currency')
                        ->label('Currency')
                        ->native(false)
                        ->default('PHP')
                        ->options([
                            'PHP' => 'PHP',
                            'USD' => 'USD',
                        ])
                        ->columnSpan(1),
                    Components\TextInput::make('subTotal')
                        ->label('SubTotal')
                        ->default('0.00')
                        ->extraInputAttributes([
                            'class' => 'text-center'
                        ])
                        ->readOnly()
                        ->dehydrated()
                        ->columnSpan(2),
                    Components\TextInput::make('total')
                        ->label('Total')
                        ->numeric()
                        ->default('0.00')
                        ->extraInputAttributes([
                            'class' => 'text-center'
                        ])
                        ->readOnly()
                        ->columnSpan(4),
                    Components\Toggle::make('vatToggle')
                        ->label('VAT')
                        ->reactive()
                        ->columnSpan(1)
                        ->inline(false)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $items = $get('items') ?? [];
                            foreach ($items as $index => $item) {
                                $baseUnitPrice = (float) ($item['unit_price'] ?? 0);
                                $quantity = (float) ($item['quantity'] ?? 0);
                                $baseSubTotal = $baseUnitPrice * $quantity;
                        
                                // Only recalculate less_amount if it's empty/null
                                $lessAmount = $item['less_amount'] ?? null;
                                $lessPercentage = (float) ($item['less_percentage'] ?? 0);
                                if ($lessAmount === null || $lessAmount === '') {
                                    $lessAmount = $baseSubTotal * ($lessPercentage / 100);
                                    $set("items.{$index}.less_amount", number_format($lessAmount, 2, '.', ''));
                                } else {
                                    $lessAmount = (float) $lessAmount;
                                }
                        
                                // Only recalculate charge_amount if it's empty/null
                                $chargeAmount = $item['charge_amount'] ?? null;
                                $chargePercentage = (float) ($item['charge_percentage'] ?? 0);
                                if ($chargeAmount === null || $chargeAmount === '') {
                                    $chargeAmount = $baseSubTotal * ($chargePercentage / 100);
                                    $set("items.{$index}.charge_amount", number_format($chargeAmount, 2, '.', ''));
                                } else {
                                    $chargeAmount = (float) $chargeAmount;
                                }
                        
                                // VAT logic for display
                                $displayUnitPrice = $state ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                $displaySubTotal = $quantity * $displayUnitPrice;
                                $set("items.{$index}.equipment_subtotal", number_format($displaySubTotal, 2, '.', ''));
                        
                                $lineTotal = $displaySubTotal + $chargeAmount - $lessAmount;
                                $set("items.{$index}.line_total", number_format($lineTotal, 2, '.', ''));
                            }
                        
                            // --- Re-fetch updated items for summary calculation ---
                            $updatedItems = $get('items') ?? [];
                            $overallSub = 0;
                            $totalChargeAmount = 0;
                            $totalLessAmount = 0;
                            foreach ($updatedItems as $item) {
                                $overallSub += (float) ($item['equipment_subtotal'] ?? 0);
                                $totalChargeAmount += (float) ($item['charge_amount'] ?? 0);
                                $totalLessAmount += (float) ($item['less_amount'] ?? 0);
                            }
                            $set('subTotal', number_format($overallSub, 2, '.', ''));
                            $set('vatAmount', $state ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));
                            $set('global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                            $set('global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                        
                            $total = $overallSub
                                + ($state ? 0 : ($overallSub * 0.12))
                                + $totalChargeAmount
                                - $totalLessAmount
                                - $get('ewt_amount');
                            $set('total', number_format($total, 2, '.', ''));
                        }),
                    Components\TextInput::make('vatAmount')
                        ->label('VAT Amount')
                        ->columnSpan(1)
                        ->default('0.00')
                        ->disabled()
                        ->dehydrated()
                ])->columns(12)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_date')
                    ->label('Invoice Date')
                    ->date(),
                
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice Number')
                    ->color('info')
                    ->formatStateUsing(fn ($state) => '83-00-' . $state),
                
                Tables\Columns\TextColumn::make('customer_id')
                    ->label('Customer')
                    ->color('primary')
                    ->formatStateUsing(function ($state) {
                        $customer = Customer::where('customer_id', $state)->first();
                        return $customer ? $customer->name : 'Unknown';
                    }),
                
                Tables\Columns\TextColumn::make('contactPerson')
                    ->label('Attention To'),
                
                Tables\Columns\TextColumn::make('carbonCopy')
                    ->label('CC'),

                Tables\Columns\TextColumn::make('poNoCalibration')
                    ->label('Purchase Order')
                    ->searchable(),

                Tables\Columns\TextColumn::make('yourRef')
                    ->label('Your Ref')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('pmsiRefNo')
                    ->label('PMSi Ref')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('freeOnBoard')
                    ->label('FOB')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('businessSystem')
                    ->label('Business System')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tin')
                    ->label('TIN #')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('service')
                    ->formatStateUsing(function ($state) {
                        $services = [
                            'calibration' => 'Calibration',
                            'repair' => 'Repair',
                            'realignment' => 'Re-alignment',
                            'cal_repair' => 'Cal / Repair',
                            'cal_realign' => 'Cal / Re-align',
                            'repair_realign' => 'Repair / Realign',
                            'cal_repair_realign' => 'Cal / Repair / Realign',
                        ];
                
                        return $services[$state] ?? 'N/A';
                    }),

                Tables\Columns\TextColumn::make('payment')
                ->formatStateUsing(fn ($record) => ($record->payment === 'cod' ? 'Cash On Delivery' : $record->payment)),

                Tables\Columns\TextColumn::make('subTotal')
                    ->money(fn ($record) => ($record->currency === 'PHP' ? 'PHP' : 'USD')),

                Tables\Columns\TextColumn::make('vatToggle')
                    ->label('Vat Inclusive')
                    ->formatStateUsing(fn ($record) => ($record->vatToggle ? 'True' : 'False'))
                    ->badge()
                    ->color(fn ($state) => ($state ? 'primary' : 'gray')),

                Tables\Columns\TextColumn::make('currency'),

                Tables\Columns\TextColumn::make('total')
                    ->money(fn ($record) => ($record->currency === 'PHP' ? 'PHP' : 'USD')),

                Tables\Columns\TextColumn::make('amountInWords')
                    ->label('Amount In Words')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('comments')
                    ->wrap()
                    ->default('No Comment'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->modalWidth(MaxWidth::ScreenTwoExtraLarge )
                        ->modalHeading(fn ($record) => (Customer::where('customer_id', $record?->customer_id)->first()?->name ?? 'Unknown'))
                        ->modalDescription('Edit Invoice'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->tooltip('Options')
                    ->color('danger'),
                Tables\Actions\Action::make('printInvoice')
                    ->tooltip('Print Invoice')
                    ->icon('bi-printer-fill')
                    ->label('Print Invoice')
                    ->requiresConfirmation()
                    ->modalIcon('bi-printer')
                    ->modalHeading('Ready to Print Invoice')
                    ->modalDescription('Make sure all invoice details are accurate. When you\'re ready, click “Print” to generate a printable copy.')
                    ->modalSubmitActionLabel('Print')
                    ->url(fn ($record) => route('invoice-manager', ['invoice_id' => $record->id]))
                    ->openUrlInNewTab(),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInvoices::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
