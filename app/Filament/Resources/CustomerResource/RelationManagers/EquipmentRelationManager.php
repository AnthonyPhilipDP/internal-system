<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EquipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'equipment';

    // To show automatically in page
    protected static bool $isLazy = false;

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('')->schema([
                        Forms\Components\Select::make('customer_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('customer', 'name')
                            ->disabled(),
                        Forms\Components\TextInput::make('manufacturer')
                            ->required()    
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('serial')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->maxLength(255),
                    ]),
                ])->columnSpan(3),
                Group::make()->schema([
                    Section::make('')->schema([
                        Forms\Components\TextInput::make('lab')
                        ->label('Laboratory')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('calType')
                        ->label('Calibration Type')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('category')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('inspection')
                            ->label('Inspection Findings')
                            ->required()
                            ->maxLength(255),
                    ]),
                ])->columnSpan(3),
                Group::make()->schema([
                    Section::make('')->schema([
                        Forms\Components\Repeater::make('accessory')
                            ->relationship()
                            ->schema([
                            Forms\Components\TextInput::make('name')
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('quantity')
                                ->numeric()
                                ->columnSpan(2),
                        ])
                        ->reorderable()
                        ->reorderableWithButtons()
                        ->reorderableWithDragAndDrop()
                        ->collapsible()
                        ->addActionLabel('Add Another Accessory')
                        ->columns(4),
                    ]),
                ])->columnSpan(3),
                Group::make()->schema([
                    Section::make('Status')->schema([
                        Forms\Components\TextInput::make('calibrationProcedure')
                            ->label('Calibration Procedure')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('previousCondition')
                            ->label('Previous Condition')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\Select::make('inCondition')
                            ->label('Condition In')
                            ->searchable()
                            ->options([
                                'asFound' => 'As Found',
                                'good' => 'In Tolerance',
                                'outOfTolerance' => 'Out of Tolerance',
                                'active' => 'Active',
                                'Inactive' => 'Inactive',
                                'damaged' => 'Damaged',
                                'rejected' => 'Rejected',
                                'returned' => 'Returned',
                                'defective' => 'Defective',
                                'inoperative' => 'Inoperative',
                                'malfunctioning' => 'Malfunctioning',
                                'brokenDisplay' => 'Broken Display',
                                'calibrated' => 'Calibrated',
                                'forRepair' => 'For Repair',
                                'forEvaluation' => 'For Evaluation',
                                'initialCalibration' => 'Initial Calibration',
                                'limitedCalibration' => 'Limited Calibration',
                                'overdueCalibration' => 'Overdue Calibration',
                                'referToReport' => 'Refer to Report',
                                'seeRemarks' => 'See Remarks',
                            ]),
                        Forms\Components\TextInput::make('Condition Out')
                            ->label('In Condition')
                            ->nullable(),
                        Forms\Components\TextInput::make('service')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\Radio::make('intermediateCheck')
                            ->label('Intermediate Check')
                            ->boolean()
                            ->inline()
                            ->inlineLabel(false)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->searchable()
                            ->options([
                                'abandoned' => 'Abandoned',
                                'completed' => 'Completed',
                                'delivered' => 'Delivered',
                                'evaluation' => 'Evaluation',
                                'repair' => 'Repair',
                                'for Sale' => 'For Sale',
                                'incoming' => 'Incoming',
                                'spareParts' => 'Spare Parts',
                                'onHold' => 'On Hold',
                                'onSite' => 'On Site',
                                'pickedUp' => 'Picked Up',
                                'Pending' => 'Pending',
                                'rejected' => 'Rejected',
                                'returned' => 'Returned',
                                'shippedOut' => 'Shipped Out',
                                'Sold' => 'Sold',
                                'transferred' => 'Transferred',
                                'unclaimed' => 'Unclaimed',
                                'audit' => 'ISO Audit',
                            ]),
                    ]),
                ])->columnSpan(5),
                Group::make()->schema([
                    Section::make('')->schema([
                        Forms\Components\TextInput::make('code_range')
                            ->label('Code | Range')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('reference')
                            ->label('Reference')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('standardsUsed')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('worksheet')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('temperature')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('humidity')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('validation')
                                ->nullable()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('validatedBy')
                                ->nullable()
                                ->maxLength(255),
                        ]),
                            //Put NCF Report here
                    ]),
                ])->columnSpan(4),
                Group::make()->schema([
                    Section::make('')->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('calibrationDate')
                                ->label('Calibration Date'),
                            Forms\Components\TextInput::make('calibrationInterval')
                                ->label('Calibration Interval')
                                ->numeric()
                                ->suffix('Months')
                                ->nullable()
                                ->maxLength(255),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('calibrationDue')
                                ->label('Calibration Due'),
                            Forms\Components\DatePicker::make('outDate')
                                ->label('Date Released'),
                        ]),
                        Forms\Components\TextInput::make('poNoCalibration')
                            ->label('Purchase Order No.')
                            ->suffix('For Calibration')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('poNoRealign')
                            ->label('Purchase Order No.')
                            ->suffix('For Realign')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('poNoRepair')
                            ->label('Purchase Order No.')
                            ->suffix('For Repair')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('prNo')
                            ->label('Purchase Receipt No.')
                            ->nullable()
                            ->maxLength(255),
                            //Document Section, make another section right here
                    ]),
                ])->columnSpan(4),   
                Group::make()->schema([
                    Section::make('Documents Update')->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('calibrationDocument')
                                ->label('Calibration Document')
                                ->nullable()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('drNoDocument')
                                ->label('Document DR No.')
                                ->nullable()
                                ->maxLength(255),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('documentReleasedDate')
                                ->label('Document Released Date'),
                            Forms\Components\TextInput::make('documentReceivedBy')
                                ->label('Document Received By')
                                ->nullable()
                                ->maxLength(255),
                        ]),
                        
                        Forms\Components\TextInput::make('ncfReport')
                            ->label('Non-conformity Report')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextArea::make('comments')
                            ->rows(2)   
                            ->autosize()
                            ->nullable()
                            ->maxLength(255),
                    ]),
                ])->columnSpan(5),
            ])->columns(9); 
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('make')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('Transaction ID')
                ->alignCenter(),
                Tables\Columns\TextColumn::make('manufacturer')
                ->alignCenter(),
                Tables\Columns\TextColumn::make('model')
                ->alignCenter(),
                Tables\Columns\TextColumn::make('serial')
                ->alignCenter(),
                Tables\Columns\TextColumn::make('description')
                ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Add create button
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
