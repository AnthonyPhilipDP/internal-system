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
use App\Models\InvoiceItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ContactPerson;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
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
                Tables\Columns\TextColumn::make('poNoCalibration')
                    ->label('PO No.')
                    ->alignCenter()
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->visible(fn () => (optional(Auth::user())->isEmployee() || optional(Auth::user())->isAdmin()) && optional(Auth::user())->has_invoice_access)
                    ->label('Generate Invoice')
                    ->closeModalByClickingAway(false)
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($records) => ('Generate Invoice for ' . $records->first()->customer->name))
                    ->modalSubheading('For precise computation and to avoid errors, kindly enter information in a step-by-step manner, following the form from top to bottom.')
                    ->modalButton('Generate')
                    ->modalIcon('bi-envelope-paper')
                    ->icon('bi-envelope-paper')
                    ->modalWidth(MaxWidth::ScreenTwoExtraLarge )
                    ->form(function ($records) {
                        // Top
                        $formSchema = [
                            Forms\Components\Fieldset::make('Customer Information')
                            ->extraAttributes([
                                'class' => 'bg-red-50'
                            ])
                            ->schema([
                                Forms\Components\Select::make('contactPerson')
                                ->label('Attention To')
                                ->native(false)
                                ->required()
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
                                ->prefix('83-00-')
                                ->default(function () {
                                    $max = Invoice::max('invoice_number');
                                    return $max ? $max + 1 : 4338; // Change the 1 value in else as the start number
                                })
                                ->disabled(),
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
                                Forms\Components\Select::make('payment')
                                ->label('Payment')
                                ->native(false)
                                ->required()
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
                            $formSchema[] = 
                            Forms\Components\Fieldset::make(fn () => "ITEM # {$itemNumber}")
                            ->extraAttributes([
                                'class' => 'bg-blue-50'
                            ])
                            ->schema([
                                Forms\Components\Grid::make(5)
                                ->schema([
                                    // First Grid
                                    Forms\Components\Group::make([
                                        Forms\Components\Fieldset::make('')
                                            ->extraAttributes([
                                                'class' => 'bg-orange-50'
                                            ])
                                            
                                            ->schema([
                                                Forms\Components\Grid::make(8)
                                                ->schema([
                                                Forms\Components\TextInput::make("item_number_{$record->id}")
                                                    ->label('Item #')
                                                    ->extraInputAttributes([
                                                        'class' => 'text-center'
                                                    ])
                                                    ->columnSpan(1)
                                                    ->default($itemNumber)
                                                    ->disabled()
                                                    ->dehydrated(),
                                                Forms\Components\TextInput::make("equipment_id_{$record->id}")
                                                    ->label('Equipment ID')
                                                    ->columnSpan(2)
                                                    ->default($record->equipment_id)
                                                    ->disabled(),
                                                Forms\Components\TextInput::make("service_{$record->id}")
                                                    ->label('Service')
                                                    ->columnSpan(3)
                                                    ->default($record->service)
                                                    ->formatStateUsing(function ($state) {
                                                        $services = [
                                                            'calibration' => 'Calibration',
                                                            'cal and realign' => 'Calibration and Realign',
                                                            'cal and repair' => 'Calibration and Repair',
                                                            'repair' => 'Repair',
                                                            'diagnostic' => 'Diagnostic',
                                                            'N/A' => 'Not Available',
                                                        ];
                                                
                                                        return $services[$state] ?? 'N/A';
                                                    })
                                                    ->disabled(),
                                                Forms\Components\TextInput::make("status_{$record->id}")
                                                    ->label('Status')
                                                    ->columnSpan(2)
                                                    ->default($record->status)
                                                    ->formatStateUsing(function ($state) {
                                                        $statuses = [
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
                                                        ];
                                                
                                                        return $statuses[$state] ?? 'N/A';
                                                    })
                                                    ->disabled(),
                                                Forms\Components\TextInput::make("make_{$record->id}")
                                                    ->label('Make')
                                                    ->columnSpan(2)
                                                    ->default($record->make)
                                                    ->disabled(),
                                                Forms\Components\TextInput::make("model_{$record->id}")
                                                    ->label('Model')
                                                    ->columnSpan(2)
                                                    ->default($record->model)
                                                    ->disabled(),
                                                Forms\Components\TextInput::make("description_{$record->id}")
                                                    ->label('Description')
                                                    ->columnSpan(2)
                                                    ->default($record->description)
                                                    ->disabled(),
                                                Forms\Components\TextInput::make("serial_{$record->id}")
                                                    ->label('Serial')
                                                    ->columnSpan(2)
                                                    ->default($record->serial)
                                                    ->disabled(),
                                                Forms\Components\TextInput::make("transaction_id_{$record->id}")
                                                    ->label('Transaction ID')
                                                    ->columnSpan(2)
                                                    ->default($record->transaction_id)
                                                    ->disabled()
                                                    ->dehydrated(),
                                                Forms\Components\TextInput::make("code_range_{$record->id}")
                                                    ->label('Code | Range')
                                                    ->columnSpan(6)
                                                    ->default($record->code_range)
                                                    ->disabled(),
                                                ])
                                            ])
                                    ])->columnSpan('2'),

                                    // Middle Grid
                                    Forms\Components\Group::make([
                                        Forms\Components\Fieldset::make('')
                                            ->extraAttributes([
                                                'class' => 'bg-orange-50'
                                            ])
                                            ->schema([
                                                Forms\Components\Grid::make(1)
                                                ->schema([
                                                    Forms\Components\TextInput::make("quantity_{$record->id}")
                                                    ->label('Quantity')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->live(debounce: 500)
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($record, $equipmentIds) {
                                                        $quantity = (float) $state;
                                                        $baseUnitPrice = (float) ($get("unit_price_{$record->id}") ?? 0);
                                                        $vatOnEquipment = $get('vatToggle');
                                                        $unitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                        $subTotal = $quantity * $unitPrice;
                                                        $set("equipment_subtotal_{$record->id}", $subTotal);
                                                    
                                                        $baseSubTotal = $baseUnitPrice * $quantity;
                                                        $lessPercentage = (float) ($get("less_percentage_{$record->id}") ?? 0);
                                                        $lessAmount = $baseSubTotal * ($lessPercentage / 100);
                                                        $set("less_amount_{$record->id}", $lessAmount);
                                                    
                                                        $chargePercentage = (float) ($get("charge_percentage_{$record->id}") ?? 0);
                                                        $chargeAmount = $baseSubTotal * ($chargePercentage / 100);
                                                        $set("charge_amount_{$record->id}", $chargeAmount);
                                                    
                                                        $lineTotal = $subTotal - $lessAmount + $chargeAmount;
                                                        $set("line_total_{$record->id}", $lineTotal);
                                                    
                                                        $overallSub = 0;
                                                        $totalChargeAmount = 0;
                                                        $totalLessAmount = 0;
                                                        foreach ($equipmentIds as $id) {
                                                            $overallSub += (float) ($get("equipment_subtotal_{$id}") ?? 0);
                                                            $totalChargeAmount += (float) ($get("charge_amount_{$id}") ?? 0);
                                                            $totalLessAmount += (float) ($get("less_amount_{$id}") ?? 0);
                                                        }

                                                        // Set subtotal and VAT
                                                        $set('subTotal', number_format($overallSub, 2, '.', ''));
                                                        $set('vatAmount', $get('vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));

                                                        // Set global less/charge amounts
                                                        $set('global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                        $set('global_less_amount', number_format($totalLessAmount, 2, '.', ''));

                                                        // Calculate and set total
                                                        $total = $overallSub
                                                            + ($get('vatToggle') ? 0 : ($overallSub * 0.12))
                                                            + $totalChargeAmount
                                                            - $totalLessAmount;
                                                        $set('total', number_format($total, 2, '.', ''));
                                                    }),
                                                Forms\Components\TextInput::make("unit_price_{$record->id}")
                                                    ->label('Unit Price')
                                                    ->numeric()
                                                    ->default('0')
                                                    ->live(debounce: 500)
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($record, $equipmentIds) {
                                                        $baseUnitPrice = (float) $state;
                                                        $vatOnEquipment = $get('vatToggle');
                                                        $unitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                        $quantity = (float) ($get("quantity_{$record->id}") ?? 0);
                                                        $subTotal = $quantity * $unitPrice;
                                                        $set("equipment_subtotal_{$record->id}", number_format($subTotal, 2, '.', ''));
                                                    
                                                        $baseSubTotal = $baseUnitPrice * $quantity;
                                                        $lessPercentage = (float) ($get("less_percentage_{$record->id}") ?? 0);
                                                        $lessAmount = $baseSubTotal * ($lessPercentage / 100);
                                                        $set("less_amount_{$record->id}", number_format($lessAmount, 2, '.', ''));
                                                    
                                                        $chargePercentage = (float) ($get("charge_percentage_{$record->id}") ?? 0);
                                                        $chargeAmount = $baseSubTotal * ($chargePercentage / 100);
                                                        $set("charge_amount_{$record->id}", number_format($chargeAmount, 2, '.', ''));
                                                    
                                                        $lineTotal = $subTotal - $lessAmount + $chargeAmount;
                                                        $set("line_total_{$record->id}", number_format($lineTotal, 2, '.', ''));
                                                    
                                                        $overallSub = 0;
                                                        $totalChargeAmount = 0;
                                                        $totalLessAmount = 0;
                                                        foreach ($equipmentIds as $id) {
                                                            $overallSub += (float) ($get("equipment_subtotal_{$id}") ?? 0);
                                                            $totalChargeAmount += (float) ($get("charge_amount_{$id}") ?? 0);
                                                            $totalLessAmount += (float) ($get("less_amount_{$id}") ?? 0);
                                                        }

                                                        // Set subtotal and VAT
                                                        $set('subTotal', number_format($overallSub, 2, '.', ''));
                                                        $set('vatAmount', $get('vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));

                                                        // Set global less/charge amounts
                                                        $set('global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                        $set('global_less_amount', number_format($totalLessAmount, 2, '.', ''));

                                                        // Calculate and set total
                                                        $total = $overallSub
                                                            + ($get('vatToggle') ? 0 : ($overallSub * 0.12))
                                                            + $totalChargeAmount
                                                            - $totalLessAmount;
                                                        $set('total', number_format($total, 2, '.', ''));
                                                    }),
                                                Forms\Components\TextInput::make("equipment_subtotal_{$record->id}")
                                                    ->label('Subtotal')
                                                    ->numeric()
                                                    ->default('0.00')
                                                    ->readOnly(),
                                                ])
                                            ])
                                    ])->columnSpan('1'),

                                    // Last Grid
                                    Forms\Components\Group::make([
                                        Forms\Components\Fieldset::make('')
                                            ->extraAttributes([
                                                'class' => 'bg-orange-50'
                                            ])
                                            ->schema([
                                                Forms\Components\Grid::make(4)
                                                ->schema([
                                                Forms\Components\Select::make("less_type_{$record->id}")
                                                    ->label('Less Type')
                                                    ->columnSpan(2)
                                                    ->native(false)
                                                    ->options([
                                                        'discount' => 'Discount',
                                                        'other' => 'Other'
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
                                                                'discount' => 'Discount',
                                                                'other' => 'Other'
                                                            ]);
                                                        }
                                                    })
                                                    ->options(function (callable $get) {
                                                        return $get('less_type_options');
                                                    }),
                                                Forms\Components\TextInput::make("less_percentage_{$record->id}")
                                                    ->label('Less (%)')
                                                    ->columnSpan(1)
                                                    ->numeric()
                                                    ->default('0.00')
                                                    ->live(debounce: 500)
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($record, $equipmentIds) {
                                                        $baseUnitPrice = (float) ($get("unit_price_{$record->id}") ?? 0);
                                                        $quantity = (float) ($get("quantity_{$record->id}") ?? 0);
                                                        $baseSubTotal = $baseUnitPrice * $quantity;
                                                    
                                                        // Calculate less_amount from percentage and set it
                                                        $lessAmount = $baseSubTotal * ((float) $state / 100);
                                                        $set("less_amount_{$record->id}", number_format($lessAmount, 2, '.', ''));
                                                    
                                                        // Now recalculate line total and summary using this less_amount
                                                        $chargeAmount = (float) ($get("charge_amount_{$record->id}") ?? 0);
                                                        $vatOnEquipment = $get('vatToggle');
                                                        $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                        $displaySubTotal = $quantity * $displayUnitPrice;
                                                        $lineTotal = $displaySubTotal + $chargeAmount - $lessAmount;
                                                        $set("line_total_{$record->id}", number_format($lineTotal, 2, '.', ''));
                                                    
                                                        // --- Summary calculation ---
                                                        $overallSub = 0;
                                                        $totalChargeAmount = 0;
                                                        $totalLessAmount = 0;
                                                        foreach ($equipmentIds as $id) {
                                                            $overallSub += (float) ($get("equipment_subtotal_{$id}") ?? 0);
                                                            $totalChargeAmount += (float) ($get("charge_amount_{$id}") ?? 0);
                                                            $totalLessAmount += (float) ($get("less_amount_{$id}") ?? 0);
                                                        }
                                                        $set('subTotal', number_format($overallSub, 2, '.', ''));
                                                        $set('vatAmount', $get('vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));
                                                        $set('global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                        $set('global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                                                    
                                                        $total = $overallSub
                                                            + ($get('vatToggle') ? 0 : ($overallSub * 0.12))
                                                            + $totalChargeAmount
                                                            - $totalLessAmount;
                                                        $set('total', number_format($total, 2, '.', ''));
                                                    }),
                                                Forms\Components\TextInput::make("less_amount_{$record->id}")
                                                    ->label('Less Amount')
                                                    ->columnSpan(1)
                                                    ->numeric()
                                                    ->default('0.00')
                                                    ->live(debounce: 500)
                                                    ->dehydrated()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($record, $equipmentIds) {
                                                        $lessAmount = (float) $state;
                                                    
                                                        // Use this lessAmount for calculations
                                                        $chargeAmount = (float) ($get("charge_amount_{$record->id}") ?? 0);
                                                        $baseUnitPrice = (float) ($get("unit_price_{$record->id}") ?? 0);
                                                        $quantity = (float) ($get("quantity_{$record->id}") ?? 0);
                                                        $vatOnEquipment = $get('vatToggle');
                                                        $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                        $displaySubTotal = $quantity * $displayUnitPrice;
                                                        $lineTotal = $displaySubTotal + $chargeAmount - $lessAmount;
                                                        $set("line_total_{$record->id}", number_format($lineTotal, 2, '.', ''));
                                                    
                                                        // --- Summary calculation ---
                                                        $overallSub = 0;
                                                        $totalChargeAmount = 0;
                                                        $totalLessAmount = 0;
                                                        foreach ($equipmentIds as $id) {
                                                            $overallSub += (float) ($get("equipment_subtotal_{$id}") ?? 0);
                                                            $totalChargeAmount += (float) ($get("charge_amount_{$id}") ?? 0);
                                                            $totalLessAmount += (float) ($get("less_amount_{$id}") ?? 0);
                                                        }
                                                        $set('subTotal', number_format($overallSub, 2, '.', ''));
                                                        $set('vatAmount', $get('vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));
                                                        $set('global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                        $set('global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                                                    
                                                        $total = $overallSub
                                                            + ($get('vatToggle') ? 0 : ($overallSub * 0.12))
                                                            + $totalChargeAmount
                                                            - $totalLessAmount;
                                                        $set('total', number_format($total, 2, '.', ''));
                                                    }),
                                                Forms\Components\Select::make("charge_type_{$record->id}")
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
                                                                'Delivery Fee' => 'Delivery Fee',
                                                                'Expedite Fee' => 'Expedite Fee',
                                                                'other' => 'Other'
                                                            ]);
                                                        }
                                                    })
                                                    ->options(function (callable $get) {
                                                        return $get('charge_type_options');
                                                    }),
                                                Forms\Components\TextInput::make("charge_percentage_{$record->id}")
                                                    ->label('Charge (%)')
                                                    ->columnSpan(1)
                                                    ->numeric()
                                                    ->default('0.00')
                                                    ->live(debounce: 500)
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($record, $equipmentIds) {
                                                        $baseUnitPrice = (float) ($get("unit_price_{$record->id}") ?? 0);
                                                        $quantity = (float) ($get("quantity_{$record->id}") ?? 0);
                                                        $baseSubTotal = $baseUnitPrice * $quantity;
                                                    
                                                        // Calculate charge_amount from percentage and set it
                                                        $chargeAmount = $baseSubTotal * ((float) $state / 100);
                                                        $set("charge_amount_{$record->id}", number_format($chargeAmount, 2, '.', ''));
                                                    
                                                        // Now recalculate line total and summary using this charge_amount
                                                        $lessAmount = (float) ($get("less_amount_{$record->id}") ?? 0);
                                                        $vatOnEquipment = $get('vatToggle');
                                                        $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                        $displaySubTotal = $quantity * $displayUnitPrice;
                                                        $lineTotal = $displaySubTotal + $chargeAmount - $lessAmount;
                                                        $set("line_total_{$record->id}", number_format($lineTotal, 2, '.', ''));
                                                    
                                                        // --- Summary calculation ---
                                                        $overallSub = 0;
                                                        $totalChargeAmount = 0;
                                                        $totalLessAmount = 0;
                                                        foreach ($equipmentIds as $id) {
                                                            $overallSub += (float) ($get("equipment_subtotal_{$id}") ?? 0);
                                                            $totalChargeAmount += (float) ($get("charge_amount_{$id}") ?? 0);
                                                            $totalLessAmount += (float) ($get("less_amount_{$id}") ?? 0);
                                                        }
                                                        $set('subTotal', number_format($overallSub, 2, '.', ''));
                                                        $set('vatAmount', $get('vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));
                                                        $set('global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                        $set('global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                                                    
                                                        $total = $overallSub
                                                            + ($get('vatToggle') ? 0 : ($overallSub * 0.12))
                                                            + $totalChargeAmount
                                                            - $totalLessAmount;
                                                        $set('total', number_format($total, 2, '.', ''));
                                                    }),
                                                Forms\Components\TextInput::make("charge_amount_{$record->id}")
                                                    ->label('Charge Amount')
                                                    ->columnSpan(1)
                                                    ->numeric()
                                                    ->default('0.00')
                                                    ->live(debounce: 500)
                                                    ->dehydrated()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($record, $equipmentIds) {
                                                        $chargeAmount = (float) $state;
                                                    
                                                        // Use this chargeAmount for calculations
                                                        $lessAmount = (float) ($get("less_amount_{$record->id}") ?? 0);
                                                        $baseUnitPrice = (float) ($get("unit_price_{$record->id}") ?? 0);
                                                        $quantity = (float) ($get("quantity_{$record->id}") ?? 0);
                                                        $vatOnEquipment = $get('vatToggle');
                                                        $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                        $displaySubTotal = $quantity * $displayUnitPrice;
                                                        $lineTotal = $displaySubTotal + $chargeAmount - $lessAmount;
                                                        $set("line_total_{$record->id}", number_format($lineTotal, 2, '.', ''));
                                                    
                                                        // --- Summary calculation ---
                                                        $overallSub = 0;
                                                        $totalChargeAmount = 0;
                                                        $totalLessAmount = 0;
                                                        foreach ($equipmentIds as $id) {
                                                            $overallSub += (float) ($get("equipment_subtotal_{$id}") ?? 0);
                                                            $totalChargeAmount += (float) ($get("charge_amount_{$id}") ?? 0);
                                                            $totalLessAmount += (float) ($get("less_amount_{$id}") ?? 0);
                                                        }
                                                        $set('subTotal', number_format($overallSub, 2, '.', ''));
                                                        $set('vatAmount', $get('vatToggle') ? number_format(0, 2, '.', '') : number_format($overallSub * 0.12, 2, '.', ''));
                                                        $set('global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                                        $set('global_less_amount', number_format($totalLessAmount, 2, '.', ''));
                                                    
                                                        $total = $overallSub
                                                            + ($get('vatToggle') ? 0 : ($overallSub * 0.12))
                                                            + $totalChargeAmount
                                                            - $totalLessAmount;
                                                        $set('total', number_format($total, 2, '.', ''));
                                                    }),
                                                Forms\Components\TextInput::make("line_total_{$record->id}")
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
                            ]);
                            $itemNumber++;
                        }

                        // Bottom 
                        $formSchema[] = Forms\Components\Fieldset::make('Invoice Computation')
                            ->extraAttributes([
                                'class' => 'bg-yellow-50'
                            ])
                            ->schema([
                                // Apply to all
                                Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Toggle::make('applyToAll')
                                        ->label('Apply less/charges to all equipment')
                                        ->columnSpan(2)
                                        ->reactive(),
                                    Forms\Components\Group::make([
                                        Forms\Components\Fieldset::make('Less')
                                        ->schema([
                                            Forms\Components\Select::make('global_less_type')
                                                ->columnSpan(2)
                                                ->native(false)
                                                ->label('Less Type')
                                                ->options([
                                                    'discount' => 'Discount',
                                                    'other' => 'Other'
                                                ])
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('new_less_type')
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
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) use ($equipmentIds) {
                                                    if ($get('applyToAll')) {
                                                        foreach ($equipmentIds as $id) {
                                                            $set("less_type_{$id}", $state);
                                                        }
                                                    }
                                                })
                                                ->options(function (callable $get) {
                                                    return $get('global_less_type_options') ?? [
                                                        'discount' => 'Discount',
                                                        'other' => 'Other'
                                                    ];
                                                }),

                                            Forms\Components\TextInput::make('global_less_percentage')
                                                ->columnSpan(1)
                                                ->label('Less (%)')
                                                ->numeric()
                                                ->default('0.00')
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) use ($equipmentIds) {
                                                    $subTotal = (float) ($get('subTotal') ?? 0);
                                                    $lessAmount = $subTotal * ((float) $state / 100);
                                                    $set('global_less_amount', number_format($lessAmount, 2, '.', ''));
                                            
                                                    // Update each equipment's line_total to reflect the global less percentage
                                                    foreach ($equipmentIds as $id) {
                                                        $baseUnitPrice = (float) ($get("unit_price_{$id}") ?? 0);
                                                        $quantity = (float) ($get("quantity_{$id}") ?? 0);
                                                        $baseSubTotal = $baseUnitPrice * $quantity;
                                            
                                                        // Calculate less_amount for this equipment (proportional to its base subtotal)
                                                        $itemLessAmount = $baseSubTotal * ((float) $state / 100);
                                                        $set("less_amount_{$id}", number_format($itemLessAmount, 2, '.', ''));
                                            
                                                        $chargeAmount = (float) ($get("charge_amount_{$id}") ?? 0);
                                                        $vatOnEquipment = $get('vatToggle');
                                                        $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                        $displaySubTotal = $quantity * $displayUnitPrice;
                                            
                                                        $lineTotal = $displaySubTotal + $chargeAmount - $itemLessAmount;
                                                        $set("line_total_{$id}", number_format($lineTotal, 2, '.', ''));
                                                    }
                                            
                                                    // Update total
                                                    $vatAmount = (float) ($get('vatAmount') ?? 0);
                                                    $globalChargeAmount = (float) ($get('global_charge_amount') ?? 0);
                                            
                                                    $total = $subTotal
                                                        + $vatAmount
                                                        + $globalChargeAmount
                                                        - $lessAmount;
                                                    $set('total', number_format($total, 2, '.', ''));
                                                }),

                                            Forms\Components\TextInput::make('global_less_amount')
                                                ->columnSpan(1)
                                                ->label('Less Amount')
                                                ->numeric()
                                                ->default('0.00')
                                                ->reactive()
                                                ->readOnly()
                                                ->afterStateHydrated(function (callable $set, callable $get) use ($equipmentIds) {
                                                    // On form load, sum all less_amounts
                                                    $sum = 0;
                                                    foreach ($equipmentIds as $id) {
                                                        $sum += (float) ($get("less_amount_{$id}") ?? 0);
                                                    }
                                                    $set('global_less_amount', number_format($sum, 2, '.', ''));
                                                })
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) use ($equipmentIds) {
                                                    // When user types, do nothing (or optionally sync to all, but you want sum)
                                                    if ($get('applyToAll')) {
                                                        $sum = 0;
                                                        foreach ($equipmentIds as $id) {
                                                            $sum += (float) ($get("less_amount_{$id}") ?? 0);
                                                        }
                                                        $set('global_less_amount', number_format($sum, 2, '.', ''));
                                                    }
                                                }),
                                            ])->columns(4)
                                        ])->visible(fn (callable $get) => $get('applyToAll')),
                                    Forms\Components\Group::make([
                                        Forms\Components\Fieldset::make('Charges')
                                        ->schema([
                                            Forms\Components\Select::make('global_charge_type')
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
                                                    Forms\Components\TextInput::make('new_charge_type')
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
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) use ($equipmentIds) {
                                                    if ($get('applyToAll')) {
                                                        foreach ($equipmentIds as $id) {
                                                            $set("charge_type_{$id}", $state);
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

                                            Forms\Components\TextInput::make('global_charge_percentage')
                                                ->columnSpan(1)
                                                ->label('Charge (%)')
                                                ->numeric()
                                                ->default('0.00')
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) use ($equipmentIds) {
                                                    $subTotal = (float) ($get('subTotal') ?? 0);
                                            
                                                    // Update each equipment's charge_amount and line_total to reflect the global charge percentage
                                                    foreach ($equipmentIds as $id) {
                                                        $baseUnitPrice = (float) ($get("unit_price_{$id}") ?? 0);
                                                        $quantity = (float) ($get("quantity_{$id}") ?? 0);
                                                        $baseSubTotal = $baseUnitPrice * $quantity;
                                            
                                                        // Calculate charge_amount for this equipment (proportional to its base subtotal)
                                                        $itemChargeAmount = $baseSubTotal * ((float) $state / 100);
                                                        $set("charge_amount_{$id}", number_format($itemChargeAmount, 2, '.', ''));
                                            
                                                        $lessAmount = (float) ($get("less_amount_{$id}") ?? 0);
                                                        $vatOnEquipment = $get('vatToggle');
                                                        $displayUnitPrice = $vatOnEquipment ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                                        $displaySubTotal = $quantity * $displayUnitPrice;
                                            
                                                        $lineTotal = $displaySubTotal + $itemChargeAmount - $lessAmount;
                                                        $set("line_total_{$id}", number_format($lineTotal, 2, '.', ''));
                                                    }
                                            
                                                    // Update summary fields
                                                    $vatAmount = (float) ($get('vatAmount') ?? 0);
                                                    $globalLessAmount = (float) ($get('global_less_amount') ?? 0);
                                            
                                                    $totalChargeAmount = 0;
                                                    foreach ($equipmentIds as $id) {
                                                        $totalChargeAmount += (float) ($get("charge_amount_{$id}") ?? 0);
                                                    }
                                                    $set('global_charge_amount', number_format($totalChargeAmount, 2, '.', ''));
                                            
                                                    $total = $subTotal
                                                        + $vatAmount
                                                        + $totalChargeAmount
                                                        - $globalLessAmount;
                                                    $set('total', number_format($total, 2, '.', ''));
                                                }),

                                            Forms\Components\TextInput::make('global_charge_amount')
                                                ->columnSpan(1)
                                                ->label('Charge Amount')
                                                ->numeric()
                                                ->default('0.00')
                                                ->reactive()
                                                ->readOnly()
                                                ->afterStateHydrated(function (callable $set, callable $get) use ($equipmentIds) {
                                                    $sum = 0;
                                                    foreach ($equipmentIds as $id) {
                                                        $sum += (float) ($get("charge_amount_{$id}") ?? 0);
                                                    }
                                                    $set('global_charge_amount', number_format($sum, 2, '.', ''));
                                                })
                                                ->afterStateUpdated(function ($state, callable $set, callable $get) use ($equipmentIds) {
                                                    if ($get('applyToAll')) {
                                                        $sum = 0;
                                                        foreach ($equipmentIds as $id) {
                                                            $sum += (float) ($get("charge_amount_{$id}") ?? 0);
                                                        }
                                                        $set('global_charge_amount', number_format($sum, 2, '.', ''));
                                                    }
                                                }),
                                            ])->columns(4)
                                        ])->visible(fn (callable $get) => $get('applyToAll')),
                                ]),

                                // Apply EWT
                                Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Toggle::make('applyEwt')
                                        ->label('Apply EWT')
                                        ->columnSpan(2)
                                        ->reactive(),
                                    Forms\Components\Group::make([
                                        Forms\Components\Fieldset::make('Less')
                                        ->schema([
                                            Forms\Components\TextInput::make('ewt_percentage')
                                                ->columnSpan(1)
                                                ->label('EWT (%)')
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

                                            Forms\Components\TextInput::make('ewt_amount')
                                                ->columnSpan(1)
                                                ->label('EWT Amount')
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
                                Forms\Components\Textarea::make('comments')
                                ->label('Comments')
                                ->rows(1)
                                ->autosize()
                                ->columnSpan(3)
                                ->placeholder('Enter any additional notes for the invoice'),
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
                                ->default('0.00')
                                ->extraInputAttributes([
                                    'class' => 'text-center'
                                ])
                                ->readOnly()
                                ->dehydrated()
                                ->columnSpan(2),
                                Forms\Components\TextInput::make('total')
                                ->label('Total')
                                ->numeric()
                                ->default('0.00')
                                ->extraInputAttributes([
                                    'class' => 'text-center'
                                ])
                                ->readOnly()
                                ->columnSpan(4),
                                Forms\Components\Toggle::make('vatToggle')
                                ->label('VAT')
                                ->reactive()
                                ->default(function () use ($records) {
                                    $customer = $records->first()->customer ?? null;
                                    return $customer ? !$customer->vatExempt : false;
                                })
                                ->columnSpan(1)
                                ->inline(false)
                                ->afterStateUpdated(function ($state, callable $set, callable $get) use ($equipmentIds) {
                                    foreach ($equipmentIds as $id) {
                                        $baseUnitPrice = (float) ($get("unit_price_{$id}") ?? 0);
                                        $quantity = (float) ($get("quantity_{$id}") ?? 0);
                                        $baseSubTotal = $baseUnitPrice * $quantity;
                                
                                        // Only recalculate less_amount if it's empty/null
                                        $lessAmount = $get("less_amount_{$id}");
                                        $lessPercentage = (float) ($get("less_percentage_{$id}") ?? 0);
                                        if ($lessAmount === null || $lessAmount === '') {
                                            $lessAmount = $baseSubTotal * ($lessPercentage / 100);
                                            $set("less_amount_{$id}", number_format($lessAmount, 2, '.', ''));
                                        } else {
                                            $lessAmount = (float) $lessAmount;
                                        }
                                
                                        // Only recalculate charge_amount if it's empty/null
                                        $chargeAmount = $get("charge_amount_{$id}");
                                        $chargePercentage = (float) ($get("charge_percentage_{$id}") ?? 0);
                                        if ($chargeAmount === null || $chargeAmount === '') {
                                            $chargeAmount = $baseSubTotal * ($chargePercentage / 100);
                                            $set("charge_amount_{$id}", number_format($chargeAmount, 2, '.', ''));
                                        } else {
                                            $chargeAmount = (float) $chargeAmount;
                                        }
                                
                                        // VAT logic for display
                                        $displayUnitPrice = $state ? $baseUnitPrice * 1.12 : $baseUnitPrice;
                                        $displaySubTotal = $quantity * $displayUnitPrice;
                                        $set("equipment_subtotal_{$id}", number_format($displaySubTotal, 2, '.', ''));
                                
                                        $lineTotal = $displaySubTotal + $chargeAmount - $lessAmount;
                                        $set("line_total_{$id}", number_format($lineTotal, 2, '.', ''));
                                    }
                                
                                    // --- Summary calculation ---
                                    $overallSub = 0;
                                    $totalChargeAmount = 0;
                                    $totalLessAmount = 0;
                                    foreach ($equipmentIds as $id) {
                                        $overallSub += (float) ($get("equipment_subtotal_{$id}") ?? 0);
                                        $totalChargeAmount += (float) ($get("charge_amount_{$id}") ?? 0);
                                        $totalLessAmount += (float) ($get("less_amount_{$id}") ?? 0);
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
                                Forms\Components\TextInput::make('vatAmount')
                                ->label('VAT Amount')
                                ->columnSpan(1)
                                ->default('0.00')
                                ->disabled()
                                ->dehydrated()
                            ])->columns(12);
                
                        return $formSchema;
                    })
                    ->action(function ($records, array $data) {
                        // 1. Create the invoice
                        $invoice = Invoice::create([
                            'customer_id'    => $this->getOwnerRecord()->customer_id,
                            'contactPerson'  => $data['contactPerson'] ?? null,
                            'carbonCopy'     => $data['carbonCopy'] ?? null,
                            'invoice_number' => $data['invoice_number'] ?? null,
                            'invoice_date'   => $data['invoice_date'] ?? null,
                            'poNoCalibration'   => $data['poNoCalibration'] ?? null,
                            'yourRef'   => $data['yourRef'] ?? null,
                            'pmsiRefNo'   => $data['pmsiRefNo'] ?? null,
                            'freeOnBoard'   => $data['freeOnBoard'] ?? null,
                            'businessSystem'   => $data['businessSystem'] ?? null,
                            'tin'   => $data['tin'] ?? null,
                            'service'   => $data['service'] ?? null,
                            'payment'   => $data['payment'] ?? null,
                            'comments'       => $data['comments'] ?? null,
                            'subTotal'       => $data['subTotal'] ?? null,
                            'vatToggle'      => $data['vatToggle'] ?? false,
                            'currency'       => $data['currency'] ?? null,
                            'total'          => $data['total'] ?? null,
                            'vatAmount'          => $data['vatAmount'] ?? null,
                            'applyToAll'  => $data['applyToAll'] ?? null,
                            'global_less_type'  => $data['global_less_type'] ?? null,
                            'global_less_percentage'  => $data['global_less_percentage'] ?? null,
                            'global_less_amount'  => $data['global_less_amount'] ?? null,
                            'global_charge_type'  => $data['global_charge_type'] ?? null,
                            'global_charge_percentage'  => $data['global_charge_percentage'] ?? null,
                            'global_charge_amount'  => $data['global_charge_amount'] ?? null,
                            'applyEwt'  => $data['applyEwt'] ?? false,
                            'ewt_percentage'  => $data['ewt_percentage'] ?? null,
                            'ewt_amount'  => $data['ewt_amount'] ?? null,
                        ]);
                
                        // 2. Loop through each equipment and create invoice items
                        foreach ($records as $record) {
                            $id = $record->id;
                            InvoiceItem::create([
                                'invoice_id'        => $invoice->id,
                                'transaction_id'    => $data["transaction_id_{$id}"] ?? null,
                                'item_number'       => $data["item_number_{$id}"] ?? null,
                                'quantity'          => $data["quantity_{$id}"] ?? 1,
                                'unit_price'        => $data["unit_price_{$id}"] ?? 0,
                                'equipment_subtotal'=> $data["equipment_subtotal_{$id}"] ?? 1,
                                'less_type'         => $data["less_type_{$id}"] ?? null,
                                'less_percentage'   => $data["less_percentage_{$id}"] ?? 0,
                                'less_amount'       => $data["less_amount_{$id}"] ?? null,
                                'charge_type'       => $data["charge_type_{$id}"] ?? null,
                                'charge_percentage' => $data["charge_percentage_{$id}"] ?? 0,
                                'charge_amount'     => $data["charge_amount_{$id}"] ?? null,
                                'line_total'        => $data["line_total_{$id}"] ?? 0,
                            ]);
                        }

                        Notification::make()
                            ->title('Invoice Has Been Succesfully Generated')
                            ->body('New Invoice has been added to the system.')
                            ->success()
                            ->icon('bi-envelope-paper')
                            ->send();
                    }),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 20, 40])
            ->extremePaginationLinks();
    }
}
