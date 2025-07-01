<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Infolists;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClientExclusive;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\EquipmentRelationManager;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'PMSi';

    protected static ?string $navigationIcon = 'bi-person-check';

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'address', 'email', 'mobile1', 'telephone1', 'name', 'nickname'];
    }

    public static function getNavigationBadge(): ?string
        {
            return static::getModel()::count();
        }

    protected static ?string $navigationBadgeTooltip = 'Total Customers';

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [
            'Customer ID' => $record->id,
            'Address' => $record->address,
            'E-mail' => $record->email,
        ];
    
        if ($record->telephone) {
            $details['Telephone'] = $record->telephone;
        }
    
        if ($record->mobile) {
            $details['Mobile'] = $record->mobile;
        }
    
        return $details;
    }

    protected static int $globalSearchResultsLimit = 5;

    // For going to view instead of the default edit url
    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return CustomerResource::getUrl('view', ['record' => $record]);
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Basic Information')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->autocomplete(false)
                                ->validationAttribute('name')
                                ->placeholder('Precision Measurement Specialists, Inc.')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('nickname')
                                ->autocomplete(false)
                                ->validationAttribute('nickname')
                                ->placeholder('PMSi')
                                ->nullable()
                                ->maxLength(20)
                                ->columnSpan(2),
                            Forms\Components\TextArea::make('address')
                                ->autocomplete(false)
                                ->validationAttribute('address')
                                ->rows(1) 
                                ->placeholder("B1 L3 Macaria Business Center. Governor's Dr., Carmona, 4116 Cavite, Philippines")
                                ->autosize()
                                ->maxLength(255)    
                                ->required()
                                ->columnSpan(3),
                            Forms\Components\DatePicker::make('dateCertified')
                                ->validationAttribute('date certified')
                                ->label('Date Certified')
                                ->nullable()
                                ->default(now())
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('tradeName')
                                ->autocomplete(false)
                                ->validationAttribute('trade name')
                                ->label('Trade Name')
                                ->nullable()
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('qualifyingSystem')
                                ->autocomplete(false)
                                ->validationAttribute('qualifying system')
                                ->label('Qualifying System')
                                ->nullable()
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('certifyingBody')
                                ->autocomplete(false)
                                ->validationAttribute('certifying body')
                                ->label('Certifying Body')
                                ->nullable()
                                ->columnSpan(1),
                            Forms\Components\TextArea::make('remarks')
                                ->autocomplete(false)
                                ->rows(1)   
                                ->autosize()
                                ->nullable()
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('referredBy')
                                ->autocomplete(false)
                                ->validationAttribute('referrer')
                                ->label('Referred By')
                                ->nullable()
                                ->columnSpan(1)
                        ])->columns(4)
                        ->icon('heroicon-o-identification')
                        ->completedIcon('heroicon-m-identification'),
                    Wizard\Step::make('BIR Information')
                    ->schema([
                            Forms\Components\TextInput::make('tin')
                                ->autocomplete(false)
                                ->validationAttribute('TIN')
                                ->label('Taxpayer Identification Number (TIN)')
                                ->validationAttribute('TIN')
                                ->required()
                                ->columnSpan(4),
                            Forms\Components\TextInput::make('sec')
                                ->autocomplete(false)
                                ->label('SEC')
                                ->nullable()
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('withHoldingTax')
                                ->autocomplete(false)
                                ->label('With Holding Tax')
                                ->nullable()
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('businessNature')
                                ->autocomplete(false)
                                ->validationAttribute('business nature')
                                ->label('Nature of Business')
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('businessStyle')
                                ->autocomplete(false)
                                ->validationAttribute('business style')
                                ->label('Business Style')
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('industry')
                                ->autocomplete(false)
                                ->validationAttribute('Line of Business / Industry')
                                ->label('Line of Business / Industry')
                                ->columnSpan(3),
                            Forms\Components\Select::make('vat')
                                ->validationAttribute('VAT')
                                ->label('VAT')
                                ->options([
                                    'VAT' => 'VAT',
                                    'Non-VAT' => 'Non-VAT',
                                ])
                                ->native(false)
                                ->nullable(fn (Get $get): bool => $get('othersForVat') || $get('vatExempt'))
                                ->disabled(fn (Get $get): bool => $get('othersForVat') || $get('vatExempt'))
                                ->columnSpan(2),
                            Forms\Components\Toggle::make('vatExempt')
                                ->label('VAT Exempt')
                                ->live()
                                ->inline(false)
                                ->extraAttributes(['class' => 'mt-2'])
                                ->disabled(fn (Get $get): bool => $get('othersForVat'))
                                ->columnSpan(1),
                            Forms\Components\Toggle::make('othersForVat')
                                ->label('Others')
                                ->live()
                                ->inline(false)
                                ->extraAttributes(['class' => 'mt-2'])
                                ->disabled(fn (Get $get): bool => $get('vatExempt'))
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('otherVat')
                                ->autocomplete(false)
                                ->validationAttribute('others VAT')
                                ->label('Please specify')
                                ->hidden(fn (Get $get): bool => ! $get('othersForVat'))
                                ->required()
                                ->columnSpan(5),
                            Forms\Components\TextInput::make('vatExemptCertificateNo')
                                ->autocomplete(false)
                                ->validationAttribute('Certificate No.')
                                ->label('Certificate No.')
                                ->hidden(fn (Get $get): bool => ! $get('vatExempt'))
                                ->required()
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('vatExemptValidity')
                                ->autocomplete(false)
                                ->validationAttribute('validity')
                                ->label('Validity')
                                ->hidden(fn (Get $get): bool => ! $get('vatExempt'))
                                ->required()
                                ->columnSpan(2),
                        ])->columns(9)
                        ->icon('heroicon-o-newspaper')
                        ->completedIcon('heroicon-m-newspaper'),
                    Wizard\Step::make('Contact Details')
                        ->schema([
                            // PhoneInput::make('phone')
                            //     ->validationAttribute('phone')
                            //     ->defaultCountry('PH')
                            //     ->initialCountry('PH')
                            //     ->default('+639')
                            //     // ->separateDialCode()
                            //     ->strictMode()
                            //     ->formatAsYouType(false)
                            //     ->required(),
                            // PhoneInput::make('landline')
                            //     ->validationAttribute('landline')
                            //     ->nullable()
                            //     ->showFlags(false)
                            //     ->disallowDropdown()
                            //     ->onlyCountries(['AF']),
                            Forms\Components\TextInput::make('mobile1')
                                ->autocomplete(false)
                                ->validationAttribute('mobile number')
                                ->label('Mobile Number (Primary)')
                                ->placeholder('Start with 09')
                                ->minLength(8)
                                ->maxLength(11)
                                ->prefix('Enter 11 digits')
                                ->tel()
                                ->nullable()
                                ->columnSpan(4),
                            Forms\Components\TextInput::make('areaCodeTelephone1')
                                ->autocomplete(false)
                                ->placeholder('e.g., 02')
                                ->validationAttribute('area code')
                                ->label('Area Code')
                                ->minLength(2)
                                ->maxLength(3)
                                ->nullable()
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('telephone1')
                                ->autocomplete(false)
                                ->placeholder('e.g., 1234567')
                                ->validationAttribute('telephone number')
                                ->tel()
                                ->label('Telephone Number (Primary)')
                                ->minLength(7)
                                ->maxLength(8)
                                ->nullable()
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('mobile2')
                                ->autocomplete(false)
                                ->label('Mobile Number (Secondary)')
                                ->validationAttribute('mobile number')
                                ->placeholder('Start with 09')
                                ->minLength(8)
                                ->maxLength(11)
                                ->prefix('Enter 11 digits')
                                ->tel()
                                ->nullable()
                                ->columnSpan(4),
                            Forms\Components\TextInput::make('areaCodeTelephone2')
                                ->autocomplete(false)
                                ->placeholder('e.g., 02')
                                ->validationAttribute('area code')
                                ->label('Area Code')
                                ->minLength(2)
                                ->maxLength(3)
                                ->nullable()
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('telephone2')
                                ->autocomplete(false)
                                ->placeholder('e.g., 1234567')
                                ->tel()
                                ->label('Telephone Number (Secondary)')
                                ->validationAttribute('telephone number')
                                ->minLength(7)
                                ->maxLength(8)
                                ->nullable()
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('email')
                                ->autocomplete(false)
                                ->validationAttribute('email')
                                ->placeholder('pmsical@yahoo.com')
                                ->email()
                                ->columnSpan(4),
                            Forms\Components\TextInput::make('website')
                                ->autocomplete(false)
                                ->placeholder('www.pmsi-cal.com')
                                ->label('Website')
                                ->suffixIcon('heroicon-m-globe-alt')
                                ->suffixIconColor('primary')
                                ->nullable()
                                ->columnSpan(4),
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'Active' => 'Active',
                                    'Inactive' => 'Inactive',
                                    'Potential' => 'Potential',
                                    'Defunct' => 'Defunct',
                                ])
                                ->default('Active')
                                ->native(false)
                                ->required()
                                ->columnSpan(4),
                            Forms\Components\Select::make('payment')
                                ->validationAttribute('payment')
                                ->options([
                                    'Cash on Delivery' => 'COD (Cash on Delivery)',
                                    'Cash upon Completion' => 'CUC (Cash upon Completion)',
                                    'Payment in Advance' => 'PIA (Payment in Advance)',
                                    'Net 7 days' => 'Net 7 days',
                                    'Net 15 days' => 'Net 15 days',
                                    'Net 30 days' => 'Net 30 days',
                                    'Net 60 days' => 'Net 60 days',
                                ])
                                ->default('Cash on Delivery')
                                ->native(false)
                                ->nullable(fn (Get $get): bool => $get('othersForPayment'))
                                ->disabled(fn (Get $get): bool => $get('othersForPayment'))
                                ->columnSpan(2),
                            Forms\Components\Toggle::make('othersForPayment')
                                ->label('Others')
                                ->live()
                                ->inline(false)
                                ->extraAttributes(['class' => 'mt-2'])
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('otherPayment')
                                ->autocomplete(false)
                                ->validationAttribute('other payment')
                                ->label('Please specify')
                                ->hidden(fn (Get $get): bool => ! $get('othersForPayment'))
                                ->required()
                                ->columnSpan(1),
                        ])->columns(8)
                        ->icon('heroicon-o-document-text')
                        ->completedIcon('heroicon-m-document-text'),
                    Wizard\Step::make('Contact Person')
                        ->schema([
                            Group::make()->schema([
                                Section::make('')->schema([
                                Forms\Components\Repeater::make('contactPerson')
                                    ->label('')
                                    ->relationship()
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
                                        ->columnSpan(4)
                                        ->required(),
                                    Forms\Components\TextInput::make('department')
                                        ->autocomplete(false)
                                        ->label('Department')
                                        ->placeholder('Department of the contact person within the company')
                                        ->columnSpan(4),
                                    Forms\Components\TextInput::make('contact2')
                                        ->autocomplete(false)
                                        ->label('Secondary Contact Number')
                                        ->placeholder('Alternative phone number')
                                        ->columnSpan(4),
                                    Forms\Components\TextInput::make('position')
                                        ->autocomplete(false)
                                        ->label('Position')
                                        ->placeholder('Position or title of the contact person within the company')
                                        ->columnSpan(4),
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
                                    ])
                                    ->reorderable()
                                    ->reorderableWithButtons()
                                    ->reorderableWithDragAndDrop()
                                    ->collapsible()
                                    ->addActionLabel('Add Contact Person')
                                    ->columns(8)
                                ]),
                            ]),
                        ])
                        ->icon('heroicon-o-device-phone-mobile')
                        ->completedIcon('heroicon-m-device-phone-mobile'),
                    ])->skippable(),
                ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_id')
                    ->label('Customer ID')
                    ->copyable()
                    ->copyMessage('Customer ID No. copied')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nickname')
                    ->label('Nickname')
                    ->copyable()
                    ->copyMessage('Customer ID No. copied')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Customer Name')
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    // ->wrap()
                    ->words(3)
                    ->searchable()
                    // ->copyable()
                    ->copyMessage('Customer Name copied'),
                Tables\Columns\TextColumn::make('address')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('mobile1')
                    ->label('Mobile')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->iconColor('primary')
                    ->copyable()
                    ->copyMessage('Mobile No. copied')
                    ->html(),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('telephone1')
                    ->label('Telephone')
                    ->icon('heroicon-o-phone')
                    ->iconColor('primary')
                    ->copyable()
                    ->copyMessage('Telephone No. copied')
                    ->html(),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary')
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500)
                    // ->wrap()
                    ->words(2)
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->label('Website')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sec')
                    ->label('SEC Reg No.')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('vat')
                    ->label('VAT'),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('withHoldingTax')
                    ->label('With Holding Tax')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('businessNature')
                    ->label('Nature of Business')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('qualifyingSystem')
                    ->label('Qualifying System')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('certifyingBody')
                    ->label('Certifying Body')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('dateCertified')
                    ->label('Date Certified')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('payment'),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('businessStyle')
                    ->label('Business System')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('tin')
                    ->label('TIN'),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('createdDate')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Date Created'),
                    // ->searchable(),
            ])->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('calibrationDue')
                    ->form([
                        Section::make('Recall Equipment')
                            ->description('Month and Year must have a selection to filter properly')
                            ->schema([
                                Forms\Components\Select::make('month')
                                    ->label('Month')
                                    ->options([
                                        '01' => 'January',
                                        '02' => 'February',
                                        '03' => 'March',
                                        '04' => 'April',
                                        '05' => 'May',
                                        '06' => 'June',
                                        '07' => 'July',
                                        '08' => 'August',
                                        '09' => 'September',
                                        '10' => 'October',
                                        '11' => 'November',
                                        '12' => 'December',
                                    ])
                                    ->native(false)
                                    ->required(),
                                Forms\Components\Select::make('year')
                                    ->label('Year')
                                    ->options(function () {
                                        $currentYear = now()->year + 1;
                                        $years = [];
                                        for ($i = $currentYear; $i >= $currentYear - 28; $i--) {
                                            $years[$i] = $i;
                                        }
                                        return $years;
                                    })
                                    ->preload()
                                    ->native(false)
                                    ->required(),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['month']) && !empty($data['year'])) {
                            // Store the filter data in the session
                            session()->put('calibrationRecallFilter', [
                                'month' => $data['month'],
                                'year' => $data['year'],
                            ]);

                            $query->whereHas('equipment', function (Builder $equipmentQuery) use ($data) {
                                $equipmentQuery
                                    ->whereMonth('calibrationDue', $data['month'])
                                    ->whereYear('calibrationDue', $data['year']);
                            });
                        }
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if (!empty($data['month'])) {
                            $indicators['month'] = 'Month: ' . \Carbon\Carbon::create()->month((int) $data['month'])->format('F');
                        }

                        if (!empty($data['year'])) {
                            $indicators['year'] = 'Year: ' . $data['year'];
                        }

                        return $indicators;
                    }),
            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->modalIcon('heroicon-o-user-minus')
                        ->modalHeading(fn (Customer $record) => 'Remove ' . $record->name)
                        ->modalDescription(fn (Customer $record) => 'Are you sure you want to remove ' . $record->name . ' as our customer?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-user-minus')
                                ->title('Customer Removed')
                                ->body('The customer has been removed successfully.'),
                        ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->modalIcon('heroicon-o-user-minus')
                        ->modalHeading(fn (Customer $record) => 'Remove ' . $record->name . ' permanently')
                        ->modalDescription(fn (Customer $record) => 'Are you sure you want to remove ' . $record->name . ' permanently as our customer?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-user-minus')
                                ->title('Customer Removed Permanently')
                                ->body('The customer has been permanently removed.'),
                        ),
                    Tables\Actions\RestoreAction::make()
                        ->color('primary')
                        ->modalIcon('heroicon-o-user-plus')
                        ->modalHeading(fn (Customer $record) => 'Bring ' . $record->name . ' back')
                        ->modalDescription(fn (Customer $record) => 'Are you sure you want to bring back ' . $record->name . ' as our customer?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-user-plus')
                                ->title('Customer Restored')
                                ->body('The customer has been restored succesfully.'),
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
                Tables\Actions\BulkAction::make('calibrationRecall')
                    ->label('Calibration Recall')
                    ->action(function ($records) {
                        // Fetch the selected customers and their associated equipment and active contact persons
                        $customerData = $records->load(['equipment', 'activeContactPerson'])->map(function ($customer) {
                            return [
                                'name' => $customer->name,
                                'telephone1' => $customer->telephone1 ?? null,
                                'telephone2' => $customer->telephone2 ?? null,
                                'mobile1' => $customer->mobile1 ?? null,
                                'mobile2' => $customer->mobile2 ?? null,
                                'email' => $customer->email,
                                'contact_persons' => $customer->activeContactPerson->map(function ($contactPerson) {
                                    return [
                                        'identity' => $contactPerson->identity,
                                        'name' => $contactPerson->name,
                                        'department' => $contactPerson->department,
                                        'position' => $contactPerson->position,
                                        'contact1' => $contactPerson->contact1,
                                        'contact2' => $contactPerson->contact2,
                                        'email' => $contactPerson->email,
                                    ];
                                })->toArray(),
                                'equipment' => $customer->equipment->filter(function ($equipment) {
                                    return $equipment->calibrationDue !== null; // Ensure calibrationDue is not null
                                })->map(function ($equipment) {
                                    $exclusiveRecord = null;
                                    if ($equipment->exclusive_id) {
                                        $exclusiveRecord = ClientExclusive::where('exclusive_id', $equipment->exclusive_id)->first();
                                    }
                                    return [
                                        'equipment_id' => $equipment->equipment_id,
                                        'transaction_id' => $equipment->transaction_id,
                                        'make' => $equipment->make,
                                        'model' => $equipment->model,
                                        'serial' => $equipment->serial,
                                        'description' => $equipment->description,
                                        'calibrationDue' => $equipment->calibrationDue,
                                        'isClientExclusive' => $equipment->isClientExclusive,
                                        'exclusive_id' => $equipment->exclusive_id,
                                        'exclusive_name' => $exclusiveRecord ? $exclusiveRecord->name : null,
                                    ];
                                })->toArray(),
                            ];
                        })->toArray();

                        // Store the data in the session
                        session()->put('calibrationRecallData', $customerData);

                        // Redirect to the Livewire component
                        return redirect()->route('recallCalibration');
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Calibration Recall for Selected Equipment')
                    ->modalSubheading('The calibration recall process is now automated to enhance efficiency and accuracy. Simply confirm the selected equipment to proceed seamlessly.')
                    ->modalButton('Confirm')
                    ->modalIcon('heroicon-o-printer')
                    ->icon('heroicon-o-printer')
                    ->color('primary'),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 20, 40])
            ->extremePaginationLinks();
    }

    public static function getRelations(): array
    {
        return [
            //Comment this if you want it on View only, go to Resource folder/Pages/ViewCustomer.php
            // EquipmentRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Customer Information')
                ->schema([
                    Infolists\Components\Grid::make(5)
                        ->schema([
                            Infolists\Components\TextEntry::make('id')
                                ->label('Client ID')
                                ->copyable(),
                            Infolists\Components\TextEntry::make('name')
                                ->label('Client Name')
                                ->copyable(),
                            Infolists\Components\TextEntry::make('address')
                                ->label('Address')
                                ->copyable(),
                                Infolists\Components\TextEntry::make('display_date')
                                ->label('Date Added')
                                ->default('Not Available')
                                ->copyable()
                                ->copyableState(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'Not Available'),
                            Infolists\Components\TextEntry::make('status')
                                ->label('Status')
                                ->formatStateUsing(fn ($state): string => match ((string) $state) {
                                    'Active' => 'Active',
                                    'Potential' => 'Potential',
                                    default => 'No Data',
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    'Active' => 'success',
                                    'Potential' => 'warning',
                                    default => 'info',
                                }),
                        ]),
                ])
                ->collapsed()
                ->compact()
                ->description('The customer information is displayed here, click to expand')
                ->icon('heroicon-m-identification')
                ->iconColor('primary'),
                Infolists\Components\Section::make('Contact Information')
                ->schema([
                    Infolists\Components\Grid::make(7)
                        ->schema([
                            Infolists\Components\TextEntry::make('activeContactPerson.name')
                                ->label('Contact Person')
                                ->listWithLineBreaks()
                                ->copyable()
                                ->copyMessage('Copied!')
                                ->copyMessageDuration(1500)
                                ->color('primary')
                                ->tooltip('Click what you want to copy'),
                            Infolists\Components\TextEntry::make('activeContactPerson.department')
                                ->label('Department')
                                ->listWithLineBreaks()
                                ->copyable()
                                ->copyMessage('Copied!')
                                ->copyMessageDuration(1500)
                                ->limit(16)
                                ->tooltip('Click what you want to copy'),
                            Infolists\Components\TextEntry::make('activeContactPerson.position')
                                ->label('Position')
                                ->listWithLineBreaks()
                                ->copyable()
                                ->copyMessage('Copied!')
                                ->copyMessageDuration(1500)
                                ->tooltip('Click what you want to copy'),
                            Infolists\Components\TextEntry::make('activeContactPerson.contact1')
                                ->label('Primary Contact')
                                ->listWithLineBreaks()
                                ->copyable()
                                ->copyMessage('Copied!')
                                ->copyMessageDuration(1500)
                                ->tooltip('Click what you want to copy'),
                            Infolists\Components\TextEntry::make('activeContactPerson.contact2')
                                ->label('Alternative Contact')
                                ->listWithLineBreaks()
                                ->copyable()
                                ->copyMessage('Copied!')
                                ->copyMessageDuration(1500)
                                ->limit(16)
                                ->tooltip('Click what you want to copy'),
                            Infolists\Components\TextEntry::make('activeContactPerson.email')
                                ->label('Email')
                                ->listWithLineBreaks()
                                ->copyable()
                                ->copyMessage('Copied!')
                                ->copyMessageDuration(1500)
                                ->color('info')
                                ->limit(16)
                                ->tooltip('Click what you want to copy'),
                            Infolists\Components\TextEntry::make('activeContactPerson.isActive')
                                ->label('Status')
                                ->listWithLineBreaks()
                                ->formatStateUsing(fn ($state): string => match ((string) $state) {
                                    '1' => 'Active',
                                    default => 'Inactive',
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    '1' => 'success',
                                    default => 'warning',
                                }),
                        ]),
                ])
                ->collapsed()
                ->compact()
                ->description('The active contact information of the customer is displayed here, click to expand')
                ->icon('heroicon-m-phone')
                ->iconColor('primary'),
            ]);
    }
}
