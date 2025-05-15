<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use DateTime;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Spatie\Color\Rgb;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Models\Equipment;
use App\Models\Worksheet;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Filament\Notifications\Notification;

use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Infolists\Components\TextEntry;
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
                Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Details')
                        ->icon('heroicon-m-cube')
                        ->schema([
                            Group::make()->schema([
                                Section::make('')->schema([
                                    // Forms\Components\Select::make('customer_id')
                                    //     ->searchable()
                                    //     ->preload()
                                    //     ->relationship('customer', 'name'),
                                    Forms\Components\TextInput::make('equipment_id')
                                        ->label('Equipment ID')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('make')
                                        ->readOnly()    
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('model')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('serial')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('description')
                                        ->maxLength(255),
                                ]),
                            ]),
                            Group::make()->schema([
                                Section::make('')->schema([
                                    Forms\Components\Select::make('laboratory')
                                        ->label('Laboratory')
                                        ->options([
                                            'electrical' => 'Electrical',
                                            'physical' => 'Physical',
                                            'repair' => 'Repair',
                                        ])
                                        ->searchable(),
                                    Forms\Components\Select::make('calibrationType')
                                        ->label('Calibration Type')
                                        ->options([
                                            'iso' => 'ISO 17025',
                                            'ansi' => 'ANSI Z540',
                                            'milstd' => 'Military Standard',
                                        ])
                                        ->searchable(),
                                    Forms\Components\Select::make('category')
                                        ->label('Category')
                                        ->options([
                                            'mass' => 'Mass',
                                            'force' => 'Force',
                                            'torque' => 'Torque',
                                            'vacuum' => 'Vacuum',
                                            'pressure' => 'Pressure',
                                            'humidity' => 'Humidity',
                                            'electrical' => 'Electrical',
                                            'dimensional' => 'Dimensional',
                                            'temperature' => 'Temperature',
                                            'conductivity' => 'Conductivity',
                                            'pcr' => 'pH / Conductivity / Resistivity',
                                        ])
                                        ->searchable(),
                                    Forms\Components\Select::make('inspection')
                                        ->validationAttribute('inspection findings')
                                        ->label('Inspection Findings')
                                        ->multiple()
                                        ->options([
                                            'no visible damage' => 'No Visible Damage',
                                            'scratches' => 'Scratches',
                                            'cracks' => 'Cracks',
                                            'grime' => 'Grime',
                                            'dents' => 'Dents',
                                            'rust' => 'Rust',
                                            'bent' => 'Bent',
                                        ]),
                                    Forms\Components\Select::make('status')
                                        ->label('Status')
                                        ->searchable()
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
                                        ]),
                                        Forms\Components\Select::make('decisionRule')
                                        ->label('Decision Rule')
                                        ->options([
                                            'default' => 'Simple Calibration',
                                            'rule1' => 'Binary Statement for Simple Acceptance Rule ( w = 0 )',
                                            'rule2' => 'Binary Statement with Guard Band( w = U )',
                                            'rule3' => 'Non-binary Statement with Guard Band( w = U )',
                                        ])
                                        ->default('default')
                                        ->native(false)
                                        ->nullable(),
                                ]),
                            ]),
                            Group::make()->schema([
                                Section::make('')->schema([
                                    Forms\Components\Repeater::make('accessory')
                                        ->relationship()
                                        ->schema([
                                        Forms\Components\TextInput::make('name'),
                                        Forms\Components\TextInput::make('quantity')
                                            //->numeric(),
                                        ])
                                        ->reorderable()
                                        ->reorderableWithButtons()
                                        ->reorderableWithDragAndDrop()
                                        ->collapsible()
                                        ->addActionLabel(function (callable $get) {
                                            $accessories = $get('accessory');
                                            return empty($accessories) ? 'Add Accessory' : 'Add Another Accessory';
                                        })
                                        ->defaultItems(0),
                                ]),
                            ]),
                        ])->columns(3),
                    Tabs\Tab::make('Status')
                        ->icon('heroicon-m-arrow-path')
                        ->schema([
                            Group::make()->schema([
                                Section::make('')->schema([
                                    Forms\Components\Select::make('worksheet')
                                        ->label('Worksheet')
                                        ->relationship('worksheet', 'name')
                                        ->getOptionLabelFromRecordUsing(function ($record) {
                                            return "{$record->name} Rev. {$record->revision}";
                                        })
                                        ->searchable(['name', 'id'])
                                        ->preload()
                                        ->prefixIcon('heroicon-o-document-check')
                                        ->prefixIconColor('primary'),
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
                                            'inTolerance' => 'In Tolerance',
                                            'outOfTolerance' => 'Out of Tolerance',
                                            'active' => 'Active',
                                            'inactive' => 'Inactive',
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
                                    Forms\Components\Select::make('outCondition')
                                        ->label('Condition Out')
                                        ->searchable()
                                        ->options([
                                            'asLeft' => 'As Left',
                                            'limitedCalibration' => 'Limited Calibration',
                                            'inTolerance' => 'In Tolerance',
                                            'outOfTolerance' => 'Out of Tolerance',
                                            'pullOut' => 'Pull Out',
                                            'brokenDisplay' => 'Broken Display',
                                            'calBeforeUse' => 'Calibrated Before Use',
                                            'conditionalCal' => 'Conditional Calibration',
                                            'defective' => 'Defective',
                                            'disposed' => 'Disposed',
                                            'ejected' => 'Ejected',
                                            'evaluation' => 'Evaluation',
                                            'verification' => 'Verification',
                                            'forReference' => 'For Reference',
                                            'forRepair' => 'For Repair',
                                            'forSale' => 'For Sale',
                                            'forSpareParts' => 'For SpParts',
                                            'inoperative' => 'Inoperative',
                                            'missing' => 'Missing',
                                            'operational' => 'Operational',
                                            'noCapability' => 'Rejected - No Capability',
                                            'returned' => 'Rejected - Returned',
                                            'disposed' => 'Rejected - Disposed',
                                            'referToReport' => 'Refer to Report',
                                            'seeRemarks' => 'See Remarks',
                                        ]),
                                    Forms\Components\TextInput::make('service')
                                        ->nullable()
                                        ->maxLength(255),
                                    Forms\Components\Toggle::make('intermediateCheck')
                                        ->label('Intermediate Check')
                                        ->onIcon('heroicon-m-check')
                                        ->offIcon('heroicon-m-x-mark'),
                                ]),
                            ])->columnSpan(1),
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
                                    Forms\Components\TextInput::make('ncfReport')
                                        ->label('Non-conformity Report')
                                        ->nullable()
                                        ->maxLength(255),
                                ]),
                            ])->columnSpan(2), 
                        ])->columns(3),
                    Tabs\Tab::make('Timeline')
                        ->icon('heroicon-m-calendar')
                        ->schema([
                            Group::make()->schema([
                                Section::make('')->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\DatePicker::make('calibrationDate')
                                            ->label('Calibration Date')
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $interval = (int) ($get('calibrationInterval') ?? 0);
                                                if ($state && $interval > 0) {
                                                    $due = \Carbon\Carbon::parse($state)->addMonths($interval)->toDateString();
                                                    $set('calibrationDue', $due);
                                                } else {
                                                    $set('calibrationDue', null);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('calibrationInterval')
                                            ->label('Calibration Interval')
                                            ->validationAttribute('calibration interval')
                                            ->numeric()
                                            ->suffix('Months')
                                            ->nullable()
                                            ->minValue(1)
                                            ->maxValue(12)
                                            ->live(debounce: 800)
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                $date = $get('calibrationDate');
                                                $interval = (int) ($state ?? 0);
                                                if ($date && $interval > 0) {
                                                    $due = \Carbon\Carbon::parse($date)->addMonths($interval)->toDateString();
                                                    $set('calibrationDue', $due);
                                                } else {
                                                    $set('calibrationDue', null);
                                                }
                                            }),
                                    ]),
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\DatePicker::make('calibrationDue')
                                            ->label('Calibration Due')
                                            ->readOnly()
                                            ->dehydrateStateUsing(function ($state, callable $get) {
                                                $date = $get('calibrationDate');
                                                $interval = (int) ($get('calibrationInterval') ?? 0);
                                                if ($date && $interval > 0) {
                                                    return \Carbon\Carbon::parse($date)->addMonths($interval)->toDateString();
                                                }
                                                return null;
                                            }),
                                        Forms\Components\DatePicker::make('outDate')
                                            ->label('Date Released'),
                                    ]),
                                    Forms\Components\Grid::make(2)->schema([
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
                                    ]),
                                ]),
                            ])->columnSpan(4), 
                        ])->columns(3),
                    Tabs\Tab::make('Documents')
                        ->icon('heroicon-m-document-text')
                        ->schema([
                            Group::make()->schema([
                                Section::make('')->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\Select::make('calibrationDocument')
                                            ->label('Calibration Document')
                                            ->nullable()
                                            ->options([
                                                'released' => 'Released',
                                                'finalized' => 'Finalized',
                                                'notApplicable' => 'Not Applicable',
                                            ])
                                            ->native(false),
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
                                    Forms\Components\TextArea::make('comments')
                                        ->rows(2)   
                                        ->autosize()
                                        ->nullable()
                                        ->maxLength(255),
                                ]),
                            ])->columnSpan(4),
                        ])->columns(4),
                ])
                ->columnSpan('full')
                ->activeTab(2)
                ->contained(false)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('make')
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment_id')
                    ->label('Equipment ID')
                    ->alignCenter()
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('make')
                    ->alignCenter()
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('model')
                    ->alignCenter()
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('description')
                    ->alignCenter()
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('serial')
                    ->alignCenter()
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('worksheet')
                    ->label('Worksheet')
                    ->alignCenter()
                    ->formatStateUsing(function ($record) {
                        return "{$record->worksheet->name} Rev. {$record->worksheet->revision}";
                    }),
            ])->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                //Add create button
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-m-pencil-square')
                        ->color('warning')
                        // ->color(Color::hex(Rgb::fromString('rgb('.Color::Pink[500].')')->toHex()))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-cube')
                                ->title('Updated Successfully')
                                ->body('The equipment data has been modified and saved successfully.'),
                        ),
                    Tables\Actions\Action::make('replicate')
                        ->label('Replicate')
                        ->action(function (Equipment $record, $data) {
                            if ($data['with_accessories']) {
                                // Replicate the Equipment record
                                $newEquipment = $record->replicate();
                                $newEquipment->save();

                                // Replicate the related Accessory records
                                foreach ($record->accessory as $accessory) {
                                    $newAccessory = $accessory->replicate();
                                    $newAccessory->equipment_id = $newEquipment->id;
                                    $newAccessory->save();
                                }
                            } else {
                                // Replicate the Equipment record without accessories
                                $newEquipment = $record->replicate();
                                $newEquipment->save();
                            }
                            // Add notification
                            Notification::make()
                            ->title('Replication Successful')
                            ->body('The equipment has been successfully replicated.')
                            ->success()
                            ->send();
                        })
                        ->form([
                            Forms\Components\Toggle::make('with_accessories')
                            ->label('Replicate with Accessories?')
                            ->default(true)
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-bolt-slash')
                            ->onColor('success')
                            ->offColor('danger')
                            ])
                            ->icon('heroicon-m-document-duplicate')
                            ->requiresConfirmation()
                            ->modalIcon('heroicon-o-document-duplicate')
                            ->modalHeading('Replicate Equipment')
                            ->modalSubheading('Do you want to replicate this equipment with accessories?')
                            ->modalButton('Replicate')
                            ->color('info'),
                            
                    Tables\Actions\Action::make('downloadWorksheet')
                        ->label('Download WS')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('info')
                        ->infolist([
                            TextEntry::make('customer.name')
                                ->Label('')
                                ->alignCenter(),
                            TextEntry::make('exclusive')
                                ->Label('')
                                ->default('N/A')
                                ->alignCenter(),
                            TextEntry::make('equipment_id')
                                ->label('')
                                ->alignCenter(),
                            TextEntry::make('make')
                                ->label('')
                                ->alignCenter(),
                            TextEntry::make('model')
                                ->label('')
                                ->alignCenter(),
                            TextEntry::make('description')
                                ->label('')
                                ->alignCenter(),
                            TextEntry::make('serial')
                                ->label('')
                                ->alignCenter(),
                            TextEntry::make('inDate')
                                ->label('')
                                ->alignCenter(),
                            TextEntry::make('transaction_id')
                                ->label('')
                                ->alignCenter()
                                ->formatStateUsing(function ($record) {
                                    return "40-{$record->transaction_id}";
                                }),
                            TextEntry::make('calibrationInterval')
                                ->label('')
                                ->alignCenter(),
                            TextEntry::make('decisionRule')
                                ->label('')
                                ->alignCenter()
                                ->formatStateUsing(function ($state) {
                                    switch ($state) {
                                        case 'default':
                                            return 'Simple Calibration';
                                        case 'rule1':
                                            return 'Binary Statement for Simple Acceptance Rule ( w = 0 )';
                                        case 'rule2':
                                            return 'Binary Statement with Guard Band( w = U )';
                                        case 'rule3':
                                            return 'Non-binary Statement with Guard Band( w = U )';
                                    }
                                }),
                        ])
                        ->requiresConfirmation()
                        ->modalHeading('Download Worksheet')
                        ->modalSubheading('You can copy the text below to paste it on the downloaded worksheet')
                        ->modalIcon('heroicon-o-arrow-down-tray')
                        ->modalSubmitAction(false)
                        ->extraModalFooterActions([
                            Tables\Actions\Action::make('download')
                                ->label('Download Worksheet')
                                ->color('info')
                                ->requiresConfirmation()
                                ->modalHeading('Download Worksheet')
                                ->modalSubheading('Confirm the download of the worksheet')
                                ->modalIcon('heroicon-o-arrow-down-tray')
                                ->action(function ($record) {
                                    $worksheetId = $record->worksheet_id;
                                    $worksheet = Worksheet::find($worksheetId);
                                
                                    if ($worksheet) {
                                        $filePath = Storage::disk('public')->path($worksheet->file);
                                
                                        // Generate the download file name
                                        $fileName = '40-' . $record->transaction_id . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                                
                                        // Return the file for download
                                        return response()->download($filePath, $fileName);
                                    } else {
                                        Notification::make()
                                            ->title('No file available')
                                            ->body('Please include a worksheet file for this equipment first.')
                                            ->danger()
                                            ->send();
                                    }
                                }),
                        ]),
                    Tables\Actions\Action::make('uploadExcel')
                        ->label('Upload Data from WS')
                        ->icon('heroicon-m-arrow-up-tray')
                        ->color('info')
                        ->form([
                            Forms\Components\FileUpload::make('excel_file')
                                ->fetchFileInformation(false)
                                ->panelAspectRatio('2:1')
                                ->label('Upload Data from Excel File')
                                ->acceptedFileTypes([
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/vnd.ms-excel'
                                ])
                                ->disk('public')
                                ->directory('temp-uploads')
                        ])
                        ->action(function (Equipment $record, array $data) {
                            try {
                                // Load the uploaded Excel file
                                $filePath = Storage::disk('public')->path($data['excel_file']);
                                $spreadsheet = IOFactory::load($filePath);
                                
                                // Get the specific worksheet
                                $sheet = $spreadsheet->getSheetByName('IS update');
                                
                                // Extract data from specific cells
                                $updateData = [
                                    'calibrationProcedure' => $sheet->getCell('B14')->getCalculatedValue(),
                                    'code_range' => $sheet->getCell('B15')->getCalculatedValue(),
                                    'reference' => $sheet->getCell('B16')->getCalculatedValue(),
                                    'standardsUsed' => $sheet->getCell('B17')->getCalculatedValue(),
                                    'validation' => $sheet->getCell('B18')->getCalculatedValue(),
                                    'validatedBy' => $sheet->getCell('B19')->getCalculatedValue(),
                                    'temperature' => $sheet->getCell('B20')->getCalculatedValue(),
                                    'humidity' => $sheet->getCell('B21')->getCalculatedValue(),
                                ];

                                // Update the equipment record
                                $record->update($updateData);

                                // Delete the temporary file
                                Storage::disk('public')->delete($data['excel_file']);

                                Notification::make()
                                    ->title('Worksheet Processed Successfully')
                                    ->body('The worksheet data has been saved as equipment details')
                                    ->success()
                                    ->send();

                            } catch (\Exception $e) {
                                // Clean up the file in case of error
                                if (isset($data['excel_file'])) {
                                    Storage::disk('public')->delete($data['excel_file']);
                                }

                                Notification::make()
                                    ->title('Error Processing Excel')
                                    ->body('There was an error processing the Excel file: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        // ->slideOver()
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-arrow-up-on-square-stack')
                        ->modalHeading(fn (Equipment $record) => 'Upload Worksheet for Equipment #' . $record->transaction_id)
                        ->modalDescription('Upload worksheet to update equipment details')
                        ->modalSubmitActionLabel('Upload and Process'), 
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-o-cog-6-tooth')
                ->tooltip('Options')
                ->color('danger')
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 20, 40])
            ->extremePaginationLinks();
    }
}
