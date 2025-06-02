<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Equipment;
use App\Models\NcfReport;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\NonConformityReportResource\Pages;
use App\Filament\Resources\NonConformityReportResource\RelationManagers;

class NonConformityReportResource extends Resource
{
    protected static ?string $model = NcfReport::class;

    protected static ?string $label = 'Non-Conformity Report';

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Equipment Details')
                    ->columns(2)
                    ->description('Check the equipment details')
                    ->schema([
                        Forms\Components\Fieldset::make('Please set the transaction ID to fetch the equipment details')
                        ->schema([
                            Forms\Components\TextInput::make('transaction_id')
                                ->extraAttributes(['class' => 'bg-red-100'])
                                ->validationAttribute('transaction ID')
                                ->label('Transaction ID')
                                ->reactive()
                                ->debounce(500)
                                ->dehydrated()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Fetch the equipment data based on the transaction_id
                                    $equipment = Equipment::where('transaction_id', $state)
                                        ->with(['customer', 'customer.contactPerson'])
                                        ->first();
                        
                                    if ($equipment) {
                                        \Log::info('Name found:' . $equipment->customer->name);
                                        $set('transaction_id', $equipment->transaction_id);
                                        $set('customerName', $equipment->customer->name);
                                        $set('contactPersonName', $equipment->customer->contactPerson->first()->name ?? 'N/A');
                                        $set('contactPersonEmail', $equipment->customer->contactPerson->first()->email ?? 'N/A');
                                        $set('ncfNumber', $equipment->transaction_id);
                                        $set('equipment_id', $equipment->equipment_id);
                                        $set('make', $equipment->make);
                                        $set('model', $equipment->model);
                                        $set('serial', $equipment->serial);
                                        $set('description', $equipment->description);
                                        $set('approvedBy', $equipment->customer->contactPerson->first()->name ?? 'N/A');
                                        $set('status', $equipment->status);
                                    } else {
                                        // Clear fields if no equipment is found
                                        $set('transaction_id', '');
                                        $set('customer_name', '');
                                        $set('contactPersonName', '');
                                        $set('contactPersonEmail', '');
                                        $set('ncfNumber', '');
                                        $set('equipment_id', '');
                                        $set('make', '');
                                        $set('model', '');
                                        $set('serial', '');
                                        $set('description', '');
                                        $set('approvedBy', '');
                                        $set('status', '');
                                    }
                                }),
                            Forms\Components\DatePicker::make('issuedDate')
                                ->label('Date Issued')
                                ->default(now()),
                        ]),
                        Forms\Components\Fieldset::make('Client & Equipment Details')
                        ->schema([
                            Forms\Components\TextInput::make('customerName')
                                ->label('Client Name'),
                            Forms\Components\TextInput::make('contactPersonName')
                                ->label('Attention to')
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\TextInput::make('contactPersonEmail')
                                ->label('Email')
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\TextInput::make('ncfNumber')
                                ->label('NCF No.')
                                ->prefix('100-')
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\TextInput::make('equipment_id')
                                ->label('Equipment ID')
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\TextInput::make('make')
                                ->label('Make')
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\TextInput::make('model')
                                ->label('Model')
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\TextInput::make('serial')
                                ->label('Serial No.')
                                ->disabled()
                                ->dehydrated(),
                            Forms\Components\TextArea::make('description')
                                ->label('Description')
                                ->rows(1)
                                ->autosize()
                                ->disabled()
                                ->dehydrated(),
                        ])
                        ->columns(3),
                    ]),
                Forms\Components\Wizard\Step::make('Non-Conformity Report')
                    ->columns(2)
                    ->description('Details of the non-conformity')
                    ->schema([
                        Forms\Components\Textarea::make('specificFailure')
                        ->validationAttribute('specific failure')
                        ->label('Specific Failure')
                        ->maxLength(255)
                        ->rows(2)
                        ->autosize()
                        ->required()
                        ->columnSpanFull(),
                        Forms\Components\Select::make('isCalibrationCompleted')
                        ->validationAttribute('calibration completion status')
                        ->label('Calibration Completed?')
                        ->options([
                            '100' => 'Yes, 100%',
                            '75' => 'No, approximately 75%',
                            '50' => 'No, approximately 50%',
                            '25' => 'No, approximately 25%',
                            '0' => 'No',
                        ])
                        ->native(false)
                        ->required(),
                        Forms\Components\Select::make('isCurrentChargeableItem')
                        ->validationAttribute('current chargeable item')
                        ->label('Current Chargeable Item?')
                        ->options([
                            'yes' => 'Yes',
                            '50' => '50% Calibration Fee',
                            'no' => 'No',
                        ])
                        ->native(false)
                        ->required(),
                        Forms\Components\Toggle::make('troubleshootingStatus')
                        ->label('Troubleshooting Status')
                        ->onIcon('heroicon-m-check')
                        ->offIcon('heroicon-m-x-mark')
                        ->onColor('success')
                        ->offColor('danger')
                        ->default(false),
                        Forms\Components\CheckboxList::make('correctiveAction')
                        ->columnSpanFull()
                        ->validationAttribute('corrective action')
                        ->label('Recommended Corrective Action')
                        ->options([
                            'action1' => 'Attempt Realignment',
                            'action2' => 'Attempt Troubleshooting',
                            'action3' => 'Limit Instrument',
                            'action4' => 'Reject Instrument',
                            'action5' => 'Provide "as found-as left" data - do not limit',
                            'action6' => 'Beyond Economical Repair (BER) - replace unit',
                        ])->columns(2)
                        ->required(),
                        Forms\Components\Section::make('')
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('diagnosticFee')
                            ->validationAttribute('diagnostic fee')
                            ->label('Diagnostic Fee')
                            ->numeric()
                            ->prefix('PHP')
                            ->required()
                            ->columnSpan(1),
                            Forms\Components\Radio::make('conditionalFee')
                            ->label('')
                            ->options([
                                'repair' => 'Repair',
                                'Realignment' => 'Realignment'
                            ])
                            ->columns(4)
                            ->default('repair')
                            ->columnSpan(2),
                            Forms\Components\TextInput::make('conditionalFeeAmount')
                            ->validationAttribute('repair or realignment fee')
                            ->label('Fee')
                            ->numeric()
                            ->prefix('PHP')
                            ->required()
                            ->columnSpan(1),
                        ]),
                    ]),
                Forms\Components\Wizard\Step::make('Handler')
                    ->description('Who manages the NCF report')
                    ->schema([
                        Forms\Components\TextInput::make('ncfReportedBy')
                        ->label('Reported By')
                        ->required(),
                        Forms\Components\TextInput::make('ncfReviewedBy')
                        ->label('Reviewed By')
                        ->required(),
                    ])
                    ->columns(2)
                    ->hiddenOn('edit'),
                Forms\Components\Wizard\Step::make('Response')
                    ->description('Client response to the NCF report')
                    ->schema([
                        // Forms\Components\TextInput::make('customerName')
                        // ->label('Client Name'),
                        Forms\Components\TextInput::make('transaction_id')
                        ->label('Transaction No.')
                        ->prefix('40-')
                        ->disabled()
                        ->dehydrated(),
                        Forms\Components\CheckboxList::make('correctiveAction')
                        ->columnSpanFull()
                        ->validationAttribute('corrective action')
                        ->label('Customer Directions')
                        ->options([
                            'action1' => 'Attempt Realignment',
                            'action2' => 'Attempt Troubleshooting',
                            'action3' => 'Limit Instrument',
                            'action4' => 'Reject Instrument',
                            'action5' => 'Provide "as found-as left" data - do not limit',
                            'action6' => 'Beyond Economical Repair (BER) - replace unit',
                        ])->columns(2)
                        ->required(),
                        Forms\Components\Textarea::make('comments')
                        ->label('Instructions / Comments')
                        ->rows(3)
                        ->autosize()
                        ->columnSpan(2),
                        Forms\Components\TextInput::make('approvedBy')
                        ->label('Approved By')
                        ->required(),
                        Forms\Components\Select::make('status')
                        ->label('Equipment Status')
                        ->native(false)
                        ->options([
                            'completed' => 'Completed',
                            'delivered' => 'Delivered',
                            'picked-up' => 'Picked-up',
                            'repair' => 'Repair',
                            'pending' => 'Pending',
                            'on-hold' => 'On-hold',
                            'incoming' => 'Incoming',
                            'returned' => 'Returned',
                            'on-site' => 'On-site',
                            'for sale' => 'For sale',
                        ])
                        ->required(),
                    ])
                    ->columns(2)
                    ->hiddenOn('create'),
            ])->skippable()
            ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ncfNumber')
                    ->label('NCF No.')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('issuedDate')
                    ->label('Date Issued')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customerName')
                    ->label('Client Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contactPersonName')
                    ->label('Contact Person')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contactPersonEmail')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('equipment_id')
                    ->label('Equipment ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('make')
                    ->label('Make')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('serial')
                    ->label('Serial No.')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('specificFailure')
                    ->label('Specific Failure')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('isCalibrationCompleted')
                    ->label('Calibration Completed')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('isCurrentChargeableItem')
                    ->label('Current Chargeable Item')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('troubleshootingStatus')
                    ->label('Troubleshooting Status')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('correctiveAction')
                    ->label('Corrective Action')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('diagnosticFee')
                    ->label('Diagnostic Fee')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('conditionalFee')
                    ->label('Conditional Fee')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('conditionalFeeAmount')
                    ->label('Conditional Fee Amount')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ncfReportedBy')
                    ->label('Reported By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ncfReviewedBy')
                    ->label('Reviewed By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comments')
                    ->label('Comments')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('repliedDate')
                    ->label('Date Replied')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approvedBy')
                    ->label('Approved By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNonConformityReports::route('/'),
            'create' => Pages\CreateNonConformityReport::route('/create'),
            'edit' => Pages\EditNonConformityReport::route('/{record}/edit'),
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
