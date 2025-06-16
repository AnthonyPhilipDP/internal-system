<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use DateTime;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Spatie\Color\Rgb;
use App\Models\Invoice;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Models\Equipment;
use App\Models\Worksheet;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ContactPerson;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Support\Enums\MaxWidth;
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
                                    Forms\Components\Select::make('service')
                                        ->label('Service')
                                        ->native(false)
                                        ->options([
                                            'calibration' => 'Calibration',
                                            'cal and realign' => 'Calibration and Realign',
                                            'cal and repair' => 'Calibration and Repair',
                                            'repair' => 'Repair',
                                            'diagnostic' => 'Diagnostic',
                                            'N/A' => 'Not Available',
                                        ]),
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
                                        Forms\Components\Select::make('drNoDocument')
                                            ->label('Calibration Document')
                                            ->nullable()
                                            ->options([
                                                '(Documents Released)' => 'Released',
                                                '(Cal report and certificate)' => 'Finalized',
                                                'Not Applicable' => 'Not Applicable',
                                            ])
                                            ->native(false),
                                        Forms\Components\TextInput::make('DrNoDocReleased')
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('make')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('worksheet')
                    ->label('Worksheet')
                    ->alignCenter(),
                    // The code will be continued upon confirming the algorithm of worksheets
                    // ->formatStateUsing(function ($record) {
                    //     return "{$record->worksheet->name} Rev. {$record->worksheet->revision}";
                    // }),
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
                Tables\Actions\BulkAction::make('setPO')
                    ->label('Set Calibration PO')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Set Purchase Order No. for the selected equipments')
                    ->modalSubheading('Enter your Purchase Order Number')
                    ->modalButton('Confirm')
                    ->modalIcon('heroicon-o-pencil-square')
                    ->icon('heroicon-o-pencil-square')
                    ->form([
                        Forms\Components\TextInput::make('poNoCalibration')
                            ->label('')
                            ->autocomplete(false)
                            ->suffix('Calibration')
                            ->nullable()
                            ->maxLength(255),
                        // Add this if needed, uncomment the foreach comment too
                        // Forms\Components\TextInput::make('poNoRealign')
                        //     ->label('')
                        //     ->autocomplete(false)
                        //     ->suffix('Realign')
                        //     ->nullable()
                        //     ->maxLength(255),
                        // Forms\Components\TextInput::make('poNoRepair')
                        //     ->label('')
                        //     ->autocomplete(false)
                        //     ->suffix('Repair')
                        //     ->nullable()
                        //     ->maxLength(255),
                    ])
                    ->action(function ($records, array $data) {
                        // Convert the collection to an array
                        $recordsArray = $records->all();
                
                        foreach ($recordsArray as $record) {
                            $record->update(['poNoCalibration' => $data['poNoCalibration']]);
                            // $record->update(['poNoRealign' => $data['poNoRealign']]);
                            // $record->update(['poNoRepair' => $data['poNoRepair']]);
                        }

                        Notification::make()
                            ->title('Assignment Successful')
                            ->body('The PO Number of equipment has been successfully assigned.')
                            ->icon('heroicon-o-pencil-square')
                            ->success()
                            ->send();
                    }),
                    Tables\Actions\BulkAction::make('generateInvoice')
                    ->label('Generate Invoice')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($records) => ('Generate Invoice for ' . $records->first()->customer->name))
                    ->modalSubheading('Review and adjust the details before generating the invoice.')
                    ->modalButton('Generate')
                    ->modalIcon('bi-envelope-paper')
                    ->icon('bi-envelope-paper')
                    ->modalWidth(MaxWidth::FitContent)
                    ->form(function ($records) {
                        // Top
                        $formSchema = [
                            Forms\Components\Fieldset::make('Customer Information')
                            ->extraAttributes([
                                'class' => 'bg-blue-50'
                            ])
                            ->schema([
                                Forms\Components\Select::make('contactPerson')
                                ->label('Attention To')
                                ->native(false)
                                ->options(function () use ($records) {
                                    // All selected equipment belong to the same customer
                                    $customerId = $records->first()->customer->id;
                                    return ContactPerson::query()
                                        ->where('customer_id', $customerId)
                                        ->latest('created_at')
                                        ->pluck('name', 'name') // 'id' is the primary key for ContactPerson
                                        ->toArray();
                                })
                                ->createOptionForm([
                                    Forms\Components\Fieldset::make('Add a new contact person for this customer')
                                    ->schema([
                                    Forms\Components\Select::make('identity')
                                        ->validationAttribute('identification')
                                        ->label('Identify As')
                                        ->columnSpan(1)
                                        ->required()
                                        ->options([
                                            'male' => 'Mr',
                                            'female' => 'Ms',
                                        ])
                                        ->native(false),
                                    Forms\Components\TextInput::make('name')
                                        ->autocomplete(false)
                                        ->validationAttribute('name')
                                        ->label('Contact Name')
                                        ->placeholder('Name of the contact person')
                                        ->columnSpan(3)
                                        ->required(),
                                    Forms\Components\TextInput::make('contact1')
                                        ->autocomplete(false)
                                        ->validationAttribute('primary contact number')
                                        ->label('Primary Contact Number')
                                        ->placeholder('Main phone number')
                                        ->length(11)
                                        ->tel()
                                        ->columnSpan(2)
                                        ->required(),
                                    Forms\Components\TextInput::make('contact2')
                                        ->autocomplete(false)
                                        ->label('Secondary Contact Number')
                                        ->placeholder('Alternative phone number')
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('department')
                                        ->autocomplete(false)
                                        ->label('Department')
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('position')
                                        ->autocomplete(false)
                                        ->label('Position')
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('email')
                                        ->autocomplete(false)
                                        ->columnSpan(4)
                                        ->email(),
                                    Forms\Components\Toggle::make('isActive')
                                        ->label('Active Status')
                                        ->onIcon('heroicon-o-bolt')
                                        ->offIcon('heroicon-o-bolt-slash')
                                        ->onColor('success')
                                        ->offColor('danger')
                                        ->default(true)
                                        ->inline(),
                                    ])->columns(4)
                                ])
                                ->createOptionUsing(function (array $data) use ($records): int {
                                    // Assuming all selected equipment belong to the same customer
                                    $customerId = $records->first()->customer->id;

                                    // Create a new contact person associated with the current customer
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
                                Forms\Components\TextInput::make('carbonCopy')
                                ->label('CC'),
                                Forms\Components\TextInput::make('invoice_number')
                                ->label('Sales Invoice')
                                ->required(),
                                Forms\Components\DatePicker::make('invoice_date')
                                ->label('Invoice Date')
                                ->required()
                                ->default(now()),
                                Forms\Components\TextInput::make('poNoCalibration')
                                ->label('Your PO'),
                                Forms\Components\TextInput::make('yourRef')
                                ->label('Your Ref'),
                                Forms\Components\TextInput::make('pmsiRefNo')
                                ->label('PMSi Ref #'),
                                Forms\Components\TextInput::make('freeOnBoard')
                                ->label('FOB'),
                                Forms\Components\TextInput::make('businessSystem')
                                ->label('Business System')
                                ->default($records->first()->customer->businessStyle),
                                Forms\Components\TextInput::make('tin')
                                ->label('TIN #')
                                ->default($records->first()->customer->tin),
                                Forms\Components\Select::make('service')
                                ->label('Service')
                                ->required()
                                ->options([
                                    '1' => '1',
                                    '2' => '2',
                                ]),
                                Forms\Components\Select::make('payment')
                                ->label('Payment')
                                ->native(false)
                                ->options([
                                    'cod' => 'Cash On Delivery'
                                ])
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('new_payment_method')
                                        ->label('Add Another Payment Method')
                                        ->required(),
                                ])
                                ->createOptionUsing(function (array $data, callable $set, callable $get): string {
                                    // Handle the creation of the new payment method
                                    $newPaymentMethod = $data['new_payment_method'];
                            
                                    // Retrieve the current options
                                    $currentOptions = $get('payment_options') ?? [];
                            
                                    // Add the new payment method to the current options
                                    $currentOptions[$newPaymentMethod] = $newPaymentMethod;
                            
                                    // Update the options for the Select component
                                    $set('payment_options', $currentOptions);
                            
                                    // Return the new payment method as the selected option
                                    return $newPaymentMethod;
                                })
                                ->reactive()
                                ->afterStateHydrated(function (callable $set, callable $get) {
                                    // Initialize the options state if not already set
                                    if (!$get('payment_options')) {
                                        $set('payment_options', [
                                            'cod' => 'Cash On Delivery',
                                            // Other predefined options can be added here
                                        ]);
                                    }
                                })
                                ->options(function (callable $get) {
                                    // Use the dynamically updated options
                                    return $get('payment_options');
                                }),
                            ])->columns(4)
                        ];

                        // Middle
                        $itemNumber = 1;
                        $equipmentIds = collect($records)->pluck('id')->all();
                        foreach ($records as $record) {
                            Forms\Components\Fieldset::make('List of Equipment')
                            ->schema([
                            $formSchema[] = Forms\Components\Grid::make(9)
                                ->schema([
                                    Forms\Components\TextInput::make("item_number_{$record->id}")
                                    ->label('Item Number')
                                    ->default($itemNumber)
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make("transaction_id_{$record->id}")
                                    ->label('Transaction ID')
                                    ->default($record->transaction_id)
                                    ->disabled(),
                                Forms\Components\TextInput::make("make_{$record->id}")
                                    ->label('Make')
                                    ->default($record->make)
                                    ->disabled(),
                                Forms\Components\TextInput::make("model_{$record->id}")
                                    ->label('Model')
                                    ->default($record->model)
                                    ->disabled(),
                                Forms\Components\TextInput::make("description_{$record->id}")
                                    ->label('Description')
                                    ->default($record->description)
                                    ->disabled(),
                                Forms\Components\TextInput::make("serial_{$record->id}")
                                    ->label('Serial')
                                    ->default($record->serial)
                                    ->disabled(),
                                Forms\Components\TextInput::make("quantity_{$record->id}")
                                    ->label('Quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($record, $equipmentIds) {
                                        // Calculate total for this equipment
                                        $quantity = $get("quantity_{$record->id}");
                                        $unitPrice = $get("unit_price_{$record->id}");
                                        $total = (float) ($quantity ?? 0) * (float) ($unitPrice ?? 0);
                                        $set("total_{$record->id}", $total);
                                    
                                        // Recalculate subtotal
                                        $subTotal = 0;
                                        foreach ($equipmentIds as $id) {
                                            $subTotal += (float) ($get("total_{$id}") ?? 0);
                                        }
                                        $set('subTotal', $subTotal);
                                    
                                        // Recalculate total (final invoice total)
                                        $less = (float) ($get('lessAmount') ?? 0);
                                        $charge = (float) ($get('chargeAmount') ?? 0);
                                        $set('total', $subTotal - $less + $charge);
                                    }),
                                Forms\Components\TextInput::make("unit_price_{$record->id}")
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->default(0)
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($record, $equipmentIds) {
                                        // Calculate total for this equipment
                                        $quantity = $get("quantity_{$record->id}");
                                        $unitPrice = $get("unit_price_{$record->id}");
                                        $total = (float) ($quantity ?? 0) * (float) ($unitPrice ?? 0);
                                        $set("total_{$record->id}", $total);
                                    
                                        // Recalculate subtotal
                                        $subTotal = 0;
                                        foreach ($equipmentIds as $id) {
                                            $subTotal += (float) ($get("total_{$id}") ?? 0);
                                        }
                                        $set('subTotal', $subTotal);
                                    
                                        // Recalculate total (final invoice total)
                                        $less = (float) ($get('lessAmount') ?? 0);
                                        $charge = (float) ($get('chargeAmount') ?? 0);
                                        $set('total', $subTotal - $less + $charge);
                                    }),
                                Forms\Components\TextInput::make("total_{$record->id}")
                                    ->label('Total')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(),
                                ])
                            ]);
                            $itemNumber++;
                        }

                        // Bottom 
                        $formSchema[] = Forms\Components\Fieldset::make('Invoice Computation')
                            ->extraAttributes([
                                'class' => 'bg-yellow-50'
                            ])
                            ->schema([
                                Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Group::make([
                                        Forms\Components\Fieldset::make('Less')
                                        ->schema([
                                            Forms\Components\Select::make('lessType')
                                                ->label('Type')
                                                ->native(false)
                                                ->options([
                                                    'discount' => 'Discount'
                                                ])
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('new_less_type')
                                                        ->label('Add Another Payment Method')
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
                                                            'discount' => 'Discount'
                                                        ]);
                                                    }
                                                })
                                                ->options(function (callable $get) {
                                                    return $get('less_type_options');
                                                }),
                                            Forms\Components\TextInput::make('lessPercentage')
                                                ->label('Less (%)')
                                                ->numeric()
                                                ->default(0)
                                                ->live(debounce: 500)
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                    $subTotal = (float) ($get('subTotal') ?? 0);
                                                    $chargeAmount = (float) ($get('chargeAmount') ?? 0);
                                                    $lessAmount = $subTotal * ((float) ($state ?? 0) / 100);
                                                    $set('lessAmount', $lessAmount);

                                                    // Always use latest less and charge
                                                    $set('total', $subTotal - $lessAmount + $chargeAmount);
                                                }),
                                            Forms\Components\TextInput::make('lessAmount')
                                                ->label('Amount')
                                                ->readOnly()
                                                ->numeric()
                                                ->default(0),
                                            ])->columns(3)
                                        ]),
                                    Forms\Components\Group::make([
                                        Forms\Components\Fieldset::make('Charges')
                                        ->schema([
                                            Forms\Components\Select::make('chargeType')
                                                ->label('Type')
                                                ->native(false)
                                                ->options([
                                                    'On-site fee' => 'On-site fee',
                                                    'Delivery Charge' => 'Delivery Charge'
                                                ])
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('new_charge_type')
                                                        ->label('Add Another Payment Method')
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
                                                            'Delivery Charge' => 'Delivery Charge'
                                                        ]);
                                                    }
                                                })
                                                ->options(function (callable $get) {
                                                    return $get('charge_type_options');
                                                }),
                                            Forms\Components\TextInput::make('chargePercentage')
                                                ->label('Charge (%)')
                                                ->numeric()
                                                ->default(0)
                                                ->live(debounce: 500)
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                    $subTotal = (float) ($get('subTotal') ?? 0);
                                                    $lessAmount = (float) ($get('lessAmount') ?? 0);
                                                    $chargeAmount = $subTotal * ((float) ($state ?? 0) / 100);
                                                    $set('chargeAmount', $chargeAmount);

                                                    // Always use latest less and charge
                                                    $set('total', $subTotal - $lessAmount + $chargeAmount);
                                                }),
                                            Forms\Components\TextInput::make('chargeAmount')
                                                ->label('Amount')
                                                ->readOnly()
                                                ->numeric()
                                                ->default(0),
                                            ])->columns(3)
                                        ]),
                                ]),
                                Forms\Components\Textarea::make('comments')
                                ->label('Comments')
                                ->rows(1)
                                ->autosize()
                                ->columnSpan(3)
                                ->placeholder('Enter any additional notes for the invoice'),
                                Forms\Components\Toggle::make('vatToggle')
                                ->label('VAT')
                                ->columnSpan(1)
                                ->inline(false),
                                Forms\Components\Select::make('currency')
                                ->label('Currency')
                                ->native(false)
                                ->default('PHP')
                                ->options([
                                    'PHP' => 'PHP',
                                    'USD' => 'USD',
                                ])
                                ->columnSpan(1),
                                Forms\Components\TextInput::make('subTotal')
                                ->label('SubTotal')
                                ->extraInputAttributes([
                                    'class' => 'text-center'
                                ])
                                ->readOnly()
                                ->dehydrated()
                                ->columnSpan(3)
                                ->afterStateHydrated(function (callable $set, callable $get) use ($equipmentIds) {
                                    // Initialize subtotal when modal opens
                                    $subTotal = 0;
                                    foreach ($equipmentIds as $id) {
                                        $subTotal += (float) ($get("total_{$id}") ?? 0);
                                    }
                                    $set('subTotal', $subTotal);
                                }),
                                Forms\Components\TextInput::make('total')
                                ->label('Total')
                                ->extraInputAttributes([
                                    'class' => 'text-center'
                                ])
                                ->readOnly()
                                ->columnSpan(4)
                                ->afterStateHydrated(function (callable $set, callable $get) {
                                    $subTotal = (float) ($get('subTotal') ?? 0);
                                    $less = (float) ($get('lessAmount') ?? 0);
                                    $charge = (float) ($get('chargeAmount') ?? 0);
                                    $set('total', $subTotal - $less + $charge);
                                }),
                            ])->columns(12);
                
                        return $formSchema;
                    })
                    ->action(function ($records, array $data) {
                        // Process the form data to generate the invoice
                        foreach ($records as $record) {
                            // For item_number
                            $itemNumberField = "item_number_{$record->id}";
                            $itemNumber = $data[$itemNumberField] ?? null;

                            $quantity = $data["quantity_{$record->id}"];
                            $unitPrice = $data["unit_price_{$record->id}"];
                            $total = $quantity * $unitPrice;
                
                            // Update the total field in the form data
                            $data["total_{$record->id}"] = $total;
                            // Save the invoice details to the database
                            Invoice::create([
                                // Top
                                'customer_id' => $record->customer_id,
                                'contactPerson' => $data['contactPerson'],
                                'carbonCopy' => $data['carbonCopy'],
                                'invoice_number' => $data['invoice_number'],
                                'invoice_date' => $data['invoice_date'],
                                'poNoCalibration' => $data['poNoCalibration'],
                                'yourRef' => $data['yourRef'],
                                'pmsiRefNo' => $data['pmsiRefNo'],
                                'freeOnBoard' => $data['freeOnBoard'],
                                'businessSystem' => $data['businessSystem'],
                                'tin' => $data['tin'],
                                'service' => $data['service'],//
                                'payment' => $data['payment'],
                                // Middle
                                'item_number' => $itemNumber,
                                'transaction_id' => $record->transaction_id,
                                'quantity' => $quantity,
                                'currency' => $data['currency'],
                                'unitPrice' => $unitPrice,
                                'equipmentTotal' => $total,
                                // 'amountInWords' => $amountInWords,//
                                // Bottom
                                'comments' => $data['comments'],
                                'subTotal' => $data['subTotal'],
                                'lessType' => $data['lessType'],
                                'lessPercentage' => $data['lessPercentage'],
                                'lessAmount' => $data['lessAmount'],
                                'chargeType' => $data['chargeType'],
                                'chargePercentage' => $data['chargePercentage'],
                                'chargeAmount' => $data['chargeAmount'],
                                'vatToggle' => $data['vatToggle'],
                                'total' => $data['total'],
                            ]);
                        }
                
                        Notification::make()
                            ->title('Invoice Generated')
                            ->body('The invoice has been successfully generated.')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 20, 40])
            ->extremePaginationLinks();
    }
}
