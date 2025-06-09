<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Spatie\Color\Rgb;
use Filament\Forms\Set;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Models\Accessory;
use App\Models\Equipment;
use Endroid\QrCode\QrCode;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Actions\Action;
use Endroid\QrCode\Writer\PngWriter;
use Filament\Forms\Components\Group;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Button;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\EquipmentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EquipmentResource\RelationManagers;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;
    
    protected static ?string $navigationGroup = 'PMSi';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $recordTitleAttribute = 'transaction_id';

    public static function getGloballySearchableAttributes(): array
    {
        return ['transaction_id', 'ar_id', 'equipment_id', 'make', 'model', 'serial', 'description'];
    }

    public static function getNavigationBadge(): ?string
        {
            return static::getModel()::where('status', 'incoming')->count();
        }

    protected static ?string $navigationBadgeTooltip = 'Incoming Equipments';

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Equipment ID' => $record->equipment_id,
            'Make' => $record->make,
            'Model' => $record->model,
            'Serial' => $record->serial,
            'Description' => $record->description,
        ];
    }

    protected static int $globalSearchResultsLimit = 5;

    public static function form(Form $form): Form
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
                                Forms\Components\Select::make('customer_id')
                                    ->label('Customer')
                                    ->searchable()
                                    ->preload()
                                    ->prefixIcon('heroicon-o-user')
                                    ->prefixIconColor('primary')
                                    ->reactive()
                                    ->options(function () {
                                        return Customer::query()
                                            ->latest('created_at')
                                            ->pluck('name', 'customer_id')
                                            ->toArray();
                                    })
                                    ->getSearchResultsUsing(function (string $search) {
                                        return Customer::query()
                                            ->where(function ($query) use ($search) {
                                                $query->where('name', 'like', "%{$search}%")
                                                    ->orWhere('nickname', 'like', "%{$search}%")
                                                    ->orWhere('customer_id', 'like', "%{$search}%");
                                            })
                                            ->pluck('name', 'customer_id')
                                            ->toArray();
                                    })
                                    ->getOptionLabelUsing(function ($value) {
                                        $customer = Customer::where('customer_id', $value)->first();
                                        return $customer ? $customer->name : null;
                                    })
                                    ->afterStateHydrated(function (?string $state, callable $get, callable $set): void {
                                        $customerId = $get('customer_id');
                                        if ($customerId) {
                                            $customer = Customer::where('customer_id', $customerId)->first();
                                            if ($customer) {
                                                $set('customerAddress', $customer->address);
                                            }
                                        }
                                    })
                                    ->afterStateUpdated(function (?string $state, callable $get, callable $set): void {
                                        if ($state) {
                                            $customer = Customer::where('customer_id', $state)->first();
                                            if ($customer) {
                                                $set('customerAddress', $customer->address);
                                            }
                                        } else {
                                            $set('customerAddress', '');
                                        }
                                    }),

                                Forms\Components\TextArea::make('customerAddress')
                                    ->label('Selected Customer Address')
                                    ->disabled()
                                    ->default(function (callable $get) {
                                        $customerId = $get('customer_id');
                                        if ($customerId) {
                                            $customer = Customer::find($customerId);
                                            return $customer ? $customer->address : '';
                                        }
                                        return ''; // Default to empty if no customer_id
                                    })
                                    ->autosize(),
                                    Forms\Components\TextInput::make('equipment_id')
                                    ->label('Equipment ID')  
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('make')
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
                                    ->default('electrical')
                                    ->native(false)
                                    ->searchable(),
                                    Forms\Components\Select::make('calibrationType')
                                    ->label('Calibration Type')
                                    ->options([
                                        'iso' => 'ISO 17025',
                                        'ansi' => 'ANSI Z540',
                                        'milstd' => 'Military Standard',
                                    ])
                                    ->default('iso')
                                    ->native(false)
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
                                    ->default('dimensional')
                                    ->native(false)
                                    ->searchable(),
                                Forms\Components\Select::make('inspection')
                                    ->validationAttribute('Visual Inspection')
                                    ->label('Visual Inspection')
                                    ->multiple()
                                    ->nullable()
                                    ->options([
                                        'no visible damage' => 'No Visible Damage',
                                        'scratches' => 'Scratches',
                                        'cracks' => 'Cracks',
                                        'grime' => 'Grime',
                                        'dents' => 'Dents',
                                        'rust' => 'Rust',
                                        'bent' => 'Bent',
                                    ]),
                                Forms\Components\DatePicker::make('inDate')
                                    ->label('Date Received')
                                    ->default(now()),
                                Forms\Components\Select::make('decisionRule')
                                    ->label('Decision Rule')
                                    ->options([
                                        'default' => 'Simple Calibration',
                                        'rule1' => 'Binary Statement for Simple Acceptance Rule ( w = 0 )',
                                        'rule2' => 'Binary Statement with Guard Band( w = U )',
                                        'rule3' => 'Non-binary Statement with Guard Band( w = U )',
                                    ])
                                    ->default('default')
                                    ->native(false),
                                Forms\Components\Select::make('status')
                                    ->label('Status')
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
                                    ->native(true)
                                    ->visibleOn('edit'),
                            ]),
                        ]),
                        Group::make()->schema([
                            Section::make('')->schema([
                                // start of ar_id for create
                                Forms\Components\Toggle::make('sameToggle')
                                    ->visibleOn('create')
                                    ->label('Generate New Receipt')
                                    ->helperText('Toggle this button to create a new acknowledgment receipt number')
                                    ->onIcon('heroicon-m-squares-plus')
                                    ->offIcon('heroicon-m-squares-2x2')
                                    ->onColor('primary')
                                    ->offColor('secondary')
                                    ->reactive()
                                    ->default(false)
                                    ->afterStateUpdated(function (bool $state, callable $get, callable $set): void {
                                        $maxAr = Equipment::query()
                                            ->selectRaw('MAX(CAST(ar_id AS UNSIGNED)) as max')
                                            ->value('max') ?? 0;
                                        // Reverse logic: If toggle false, use max; otherwise, increment by one.
                                        $newValue = !$state ? $maxAr : ((int)$maxAr + 1);
                                        $set('ar_id', (string)$newValue);

                                        if (!$state) {
                                            $customerId = Equipment::query()
                                                ->where('ar_id', $maxAr)
                                                ->value('customer_id');
                                            $set('customer_id', $customerId);
                                        
                                            // Fetch and set the customer's address based on the customer_id
                                            if ($customerId) {
                                                $customer = Customer::find($customerId);
                                                if ($customer) {
                                                    $set('customerAddress', $customer->address);
                                                }
                                            }
                                        } else {
                                            // Set customer_id to null and clear address when toggle is on
                                            $set('customer_id', null);
                                            $set('customerAddress', '');
                                        }
                                    }),
                                // TextInput for ar_id: shows computed value and updates on hydration.
                                // end of ar_id for create
                                // start of ar_id for edit
                                Forms\Components\TextInput::make('ar_id')
                                    ->label('Acknowledgement Receipt No.')
                                    ->prefix('401 -')
                                    ->maxLength(255)
                                    ->readonly(fn (callable $get) => !$get('enableEditing') && !blank($get('ar_id')))
                                    ->dehydrated()
                                    ->reactive()
                                    ->numeric()
                                    ->afterStateHydrated(function (?string $state, callable $get, callable $set) {
                                        // Only auto-set on create (when ar_id is empty)
                                        if (blank($state)) {
                                            $maxAr = Equipment::query()
                                                ->selectRaw('MAX(CAST(ar_id AS UNSIGNED)) as max')
                                                ->value('max') ?? 0;
                                            $toggle = $get('sameToggle');
                                            $newValue = !$toggle ? $maxAr : ((int)$maxAr + 1);
                                            $set('ar_id', (string)$newValue);

                                            if (!$toggle) {
                                                $customerId = Equipment::query()
                                                    ->where('ar_id', $maxAr)
                                                    ->value('customer_id');
                                                $set('customer_id', $customerId);
                                            }
                                        }
                                    }),
                                Forms\Components\Toggle::make('enableEditing')
                                    ->visibleOn('edit')
                                    ->label('Enable Editing')
                                    ->onIcon('heroicon-m-lock-open')
                                    ->offIcon('heroicon-m-lock-closed')
                                    ->helperText('Toggle this button to edit the Acknowledgement Receipt No.')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->reactive()
                                    ->default(false)
                                    ->afterStateUpdated(function (bool $state, callable $get, callable $set): void {
                                        if ($state) {
                                            // Store the original value when the toggle is turned on
                                            $originalValue = $get('ar_id');
                                            $set('original_ar_id', $originalValue);
                                        } else {
                                            // Restore the original value when the toggle is turned off
                                            $originalValue = $get('original_ar_id');
                                            $set('ar_id', $originalValue);
                                        }
                                    }),
                                // end of ar_id for edit 
                                Forms\Components\TextInput::make('gatePass')
                                    ->label('Gate Pass')
                                    ->maxLength(255),
                                Forms\Components\TextArea::make('oldInspection')
                                    ->readOnly()
                                    ->label('Old Inspection Findings')
                                    ->autosize()
                                    ->rows(1)
                                    ->extraAttributes(['class' => 'bg-emerald-50 dark:bg-emerald-900'])
                                    ->visible(fn ($get) => $get('isEquipmentImported') == 1),
                                Forms\Components\TextArea::make('oldAccessories')
                                    ->readOnly()
                                    ->label('Old Accessories')
                                    ->autosize()
                                    ->rows(1)
                                    ->extraAttributes(['class' => 'bg-emerald-50 dark:bg-emerald-900'])
                                    ->helperText('Old accessories and visual inspection will be shown here - UNEDITABLE.')
                                    ->visible(fn ($get) => $get('isEquipmentImported') == 1),
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
                                    ->addActionLabel(function (callable $get) {
                                        $accessories = $get('accessory');
                                        return empty($accessories) ? 'Add Accessory' : 'Add Another Accessory';
                                    })
                                    ->defaultItems(0)
                                    ->columns(4),
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
            ->activeTab(1)
            ->contained(false)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->alignCenter()
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        /*
                            'incoming' => 'Incoming',
                            'pending' => 'Pending',
                            'delivered' => 'Delivered',
                            'abandoned' => 'Abandoned',
                            'completed' => 'Completed',
                            'evaluation' => 'Evaluation',
                            'repair' => 'Repair',
                            'forSale' => 'For Sale',
                            'spareParts' => 'Spare Parts',
                            'onHold' => 'On Hold',
                            'onSite' => 'On Site',
                            'pickedUp' => 'Picked Up',
                            'rejected' => 'Rejected',
                            'returned' => 'Returned',
                            'shippedOut' => 'Shipped Out',
                            'Sold' => 'Sold',
                            'transferred' => 'Transferred',
                            'unclaimed' => 'Unclaimed',
                            'audit' => 'ISO Audit',
                        */
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
                Tables\Columns\SelectColumn::make('service')
                    ->label('Service')
                    ->options([
                        'calibration' => 'Calibration',
                        'cal and realign' => 'Calibration and Realign',
                        'cal and repair' => 'Calibration and Repair',
                        'repair' => 'Repair',
                        'diagnostic' => 'Diagnostic',
                        'N/A' => 'Not Available',
                    ]),
                Tables\Columns\TextColumn::make('ar_id')
                    ->label('Receipt ID')
                    ->alignCenter()
                    ->numeric()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state) {
                        return '401-' . $state;
                    }),
                Tables\Columns\TextColumn::make('equipment_id')
                    ->label('Equipment ID')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer Name')
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    ->words(3)
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('worksheet.name')
                    ->label('Worksheet')
                    ->default('No worksheet yet!')
                    ->alignCenter()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('make')
                    ->alignCenter()
                    ->label('Make')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('inspection')
                    ->alignCenter()
                    ->label('Inspection Findings')
                    //This is just for capitalizing the words in the array
                    ->formatStateUsing(function ($state): string {return ucwords($state);})
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('laboratory')
                    ->label('Laboratory')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('calibrationType')
                    ->label('Calibration Type')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('accessory.name')
                    ->listWithLineBreaks()
                    ->bulleted(),
                Tables\Columns\TextColumn::make('accessory.quantity')
                    ->label('Quantity')
                    ->listWithLineBreaks()
                    ->bulleted(),
                Tables\Columns\TextColumn::make('created_at')
                    ->alignCenter()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->alignCenter()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-m-pencil-square')
                        // ->color(Color::hex(Rgb::fromString('rgb('.Color::Pink[500].')')->toHex())),
                        ->color('warning'),
                    Tables\Actions\Action::make('duplicate')
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

                            // Generate QR code for the new equipment
                            EquipmentResource::generateQrCode($newEquipment);

                            // Add notification
                            Notification::make()
                                ->title('Replication Successful')
                                ->body('The equipment has been successfully replicated.')
                                ->icon('heroicon-o-document-duplicate')
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
                    Tables\Actions\Action::make('onsite_duplicate')
                        ->label('On-site Replicate')
                        ->icon('heroicon-m-document-duplicate')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-document-duplicate')
                        ->modalHeading('On-site Replicate Equipment')
                        ->modalSubheading('This will replicate only fields needed for on-site use.')
                        ->modalButton('On-site Replicate')
                        ->color('primary')
                        ->action(function (Equipment $record) {
                            // Only replicate the specified fields
                            $fieldsToCopy = [
                                'equipment_id',
                                'customer_id',
                                'make',
                                'model',
                                'description',
                                'serial',
                                'laboratory',
                                'category',
                                'calibrationProcedure',
                                'code_range',
                                'reference',
                            ];

                            $newEquipment = new Equipment();
                            foreach ($fieldsToCopy as $field) {
                                $newEquipment->$field = $record->$field;
                            }
                            $newEquipment->save();

                            // Generate QR code for the new equipment
                            EquipmentResource::generateQrCode($newEquipment);

                            // Add notification
                            Notification::make()
                                ->title('On-site Replication Successful')
                                ->body('The equipment has been successfully replicated for on-site use.')
                                ->icon('heroicon-o-document-duplicate')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->label('Delete')
                        ->modalIcon('heroicon-o-trash')
                        ->modalHeading(fn (Equipment $record) => 'Remove ' . $record->make)
                        ->modalDescription(fn (Equipment $record) => 'Are you sure you want to remove ' . $record->make . ' equipment?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-trash')
                                ->title('Equipment Removed')
                                ->body('The equipment has been removed successfully.'),
                        ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->modalIcon('heroicon-o-trash')
                        ->modalHeading(fn (Equipment $record) => 'Remove ' . $record->make . ' permanently?')
                        ->modalDescription(fn (Equipment $record) => 'Are you sure you want to remove ' . $record->make . ' equipment permanently?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-trash')
                                ->title('Equipment Removed Permanently')
                                ->body('The equipment has been permanently removed.'),
                        ),
                    Tables\Actions\RestoreAction::make()
                        ->color('primary')
                        ->modalIcon('heroicon-o-cube')
                        ->modalHeading(fn (Equipment $record) => 'Bring ' . $record->make . ' back')
                        ->modalDescription(fn (Equipment $record) => 'Are you sure you want to bring back ' . $record->make . ' in our equipments?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-cube')
                                ->title('Equipment Restored')
                                ->body('The equipment has been restored succesfully.'),
                        ),
                ])
                ->icon('heroicon-o-ellipsis-horizontal-circle')
                ->tooltip('Options') 
                ->color('danger')
                ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
                Tables\Actions\BulkAction::make('printCertificate')
                    ->label('Print Certificate')
                    ->requiresConfirmation()
                    ->modalHeading('Print Certificate for Selected Equipment')
                    ->modalSubheading('Choose the options below to customize your certificate printing. The selections will be applied to all selected equipment.')
                    ->modalButton('Confirm')
                    ->modalIcon('heroicon-o-printer')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->modalWidth(MaxWidth::Large)
                    ->form([
                        Forms\Components\Group::make([
                            Forms\Components\Toggle::make('withPabLogo')
                                ->onIcon('heroicon-m-bolt')
                                ->offIcon('heroicon-m-bolt-slash')
                                ->onColor('success')
                                ->offColor('danger')
                                ->label('With PAB Logo')
                                ->default(true),
                            Forms\Components\Toggle::make('withCalibrationDue')
                                ->onIcon('heroicon-m-bolt')
                                ->offIcon('heroicon-m-bolt-slash')
                                ->onColor('success')
                                ->offColor('danger')
                                ->label('With Calibration Due')
                                ->default(true),
                        ])->columns(1)
                    ])
                    ->action(function ($records, $data) {
                        $equipmentData = $records->map(function ($record) use ($data) {
                            return [
                                'id' => $record->id,
                                'transaction_id' => $record->transaction_id,
                                'customer_id' => $record->customer_id,
                                'customer_name' => $record->customer->name,
                                'customer_address' => $record->customer->address,
                                'equipment_id' => $record->equipment_id,
                                'make' => $record->make,
                                'model' => $record->model,
                                'description' => $record->description,
                                'serial' => $record->serial,
                                'inDate' => $record->inDate,
                                'calibrationDate' => $record->calibrationDate,
                                'calibrationDue' => $record->calibrationDue,
                                'calibrationProcedure' => $record->calibrationProcedure,
                                'temperature' => $record->temperature,
                                'humidity' => $record->humidity,
                                'validation' => $record->validation,
                                'inCondition' => $record->inCondition,
                                'outCondition' => $record->outCondition,
                                'withPabLogo' => $data['withPabLogo'],
                                'withCalibrationDue' => $data['withCalibrationDue'],
                            ];
                        })->toArray();
                
                        session(['selectedCertificateData' => $equipmentData]);
                
                        return redirect('/equipment/certificate');
                    }),
                Tables\Actions\BulkAction::make('printLabel')
                ->label('Print Label')
                ->action(function ($records) {
                    $equipmentData = $records->map(function ($record) {
                        return [
                            'id' => $record->id,
                            'transaction_id' => $record->transaction_id,
                            'customer_id' => $record->customer_id,
                            'equipment_id' => $record->equipment_id,
                            'inDate' => $record->inDate,
                            'has_accessory' => $record->accessory()->exists(),
                        ];
                    })->toArray();

                    session(['selectedEquipmentData' => $equipmentData]);

                    return redirect('/equipment/print-label');
                })
                ->requiresConfirmation()
                ->modalHeading('Print Label for Selected Equipment')
                ->modalSubheading('The labeling process is now automated to enhance efficiency and accuracy. Simply confirm the selected equipments to proceed seamlessly')
                ->modalButton('Confirm')
                ->modalIcon('heroicon-o-printer')
                ->icon('heroicon-o-printer')
                ->color('info'),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 20, 40])
            ->extremePaginationLinks();
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
            'index' => Pages\ListEquipment::route('/'),
            'create' => Pages\CreateEquipment::route('/create'),
            'edit' => Pages\EditEquipment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    protected static function generateQrCode($equipment)
    {
        $qrData = $equipment->id;

        $qrCode = new QrCode($qrData);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $fileName = 'qrcodes/equipment_' . $equipment->id . '.png';
        Storage::disk('public')->put($fileName, $result->getString());
    }
}
