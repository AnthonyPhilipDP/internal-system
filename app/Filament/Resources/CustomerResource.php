<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
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

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'address', 'email', 'mobile1', 'telephone1', 'name', 'nickname'];
    }

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
                                ->validationAttribute('name')
                                ->placeholder('Precision Measurement Specialists, Inc.')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('nickname')
                                ->validationAttribute('nickname')
                                ->nullable()
                                ->maxLength(20)
                                ->columnSpan(2),
                            Forms\Components\TextArea::make('address')
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
                            Forms\Components\TextInput::make('qualifyingSystem')
                                ->validationAttribute('qualifying system')
                                ->label('Qualifying System')
                                ->nullable()
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('certifyingBody')
                                ->validationAttribute('certifying body')
                                ->label('Certifying Body')
                                ->nullable()
                                ->columnSpan(2),
                            Forms\Components\TextArea::make('remarks')
                                ->rows(1)   
                                ->autosize()
                                ->nullable()
                                ->columnSpan(4),
                        ])->columns(4)
                        ->icon('heroicon-o-identification')
                        ->completedIcon('heroicon-m-identification'),
                    Wizard\Step::make('BIR Information')
                        ->schema([
                            Forms\Components\TextInput::make('tin')
                                ->validationAttribute('TIN')
                                ->label('Taxpayer Identification Number')
                                ->validationAttribute('TIN')
                                ->required(),
                            Forms\Components\TextInput::make('sec')
                                ->label('SEC Reg no.')
                                ->required(),
                            Forms\Components\Select::make('vat')
                                ->validationAttribute('VAT')
                                ->label('VAT')
                                ->options([
                                    'VAT' => 'VAT',
                                    'Non-VAT' => 'Non-VAT',
                                ])
                                ->native(false)
                                ->required(),
                            Forms\Components\TextInput::make('withHoldingTax')
                                ->label('With Holding Tax')
                                ->required(),
                            Forms\Components\TextInput::make('businessNature')
                                ->validationAttribute('business nature')
                                ->label('Nature of Business')
                                ->required(),
                            Forms\Components\TextInput::make('businessStyle')
                                ->validationAttribute('business style')
                                ->label('Business Style')
                                ->required(),
                        ])->columns(2)
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
                                ->validationAttribute('mobile number')
                                ->label('Mobile Number (Primary)')
                                ->placeholder('Start with 09')
                                ->prefix('Enter 11 digits')
                                ->tel()
                                ->nullable(),
                            Forms\Components\TextInput::make('telephone1')
                                ->validationAttribute('telephone number')
                                ->tel()
                                ->label('Telephone Number (Primary)')
                                ->prefix('Enter 10 digits')
                                ->length(10)
                                ->nullable(),
                            Forms\Components\TextInput::make('mobile2')
                                ->label('Mobile Number (Secondary)')
                                ->validationAttribute('mobile number')
                                ->placeholder('Start with 09')
                                ->length(11)
                                ->prefix('Enter 11 digits')
                                ->tel()
                                ->nullable(),
                            Forms\Components\TextInput::make('telephone2')
                                ->tel()
                                ->label('Telephone Number (Secondary)')
                                ->validationAttribute('telephone number')
                                ->prefix('Enter 10 digits')
                                ->length(10)
                                ->nullable(),
                            Forms\Components\TextInput::make('email')
                                ->validationAttribute('email')
                                ->placeholder('pmsical@yahoo.com')
                                ->email()
                                ->required(),
                            Forms\Components\TextInput::make('website')
                                ->placeholder('pmsi-cal.com')
                                ->label('Website')
                                ->prefix('www')
                                ->suffixIcon('heroicon-m-globe-alt')
                                ->suffixIconColor('primary')
                                ->nullable(),
                            Forms\Components\Select::make('payment')
                                ->validationAttribute('payment')
                                ->options([
                                    'Cash on Delivery' => 'Cash on Delivery',
                                    'Net 7 days' => 'Net 7 days',
                                    'Net 15 days' => 'Net 15 days',
                                    'Net 30 days' => 'Net 30 days',
                                    'Net 60 days' => 'Net 60 days',
                                ])
                                ->default('Cash on Delivery')
                                ->native(false)
                                ->required(),
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'Active' => 'Active',
                                    'Potential' => 'Potential',
                                ])
                                ->default('Active')
                                ->native(false)
                                ->required(),
                        ])->columns(2)
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
                                    Forms\Components\TextInput::make('name')
                                        ->validationAttribute('name')
                                        ->label('Contact Name')
                                        ->placeholder('Name of the contact person')
                                        ->columnSpan(2)
                                        ->required(),
                                    Forms\Components\TextInput::make('contact1')
                                        ->validationAttribute('primary contact number')
                                        ->label('Primary Contact Number')
                                        ->placeholder('Main phone number')
                                        ->length(11)
                                        ->tel()
                                        ->columnSpan(2)
                                        ->required(),
                                    Forms\Components\TextInput::make('department')
                                        ->label('Department')
                                        ->placeholder('Department of the contact person within the company')
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('contact2')
                                        ->label('Secondary Contact Number')
                                        ->placeholder('Alternative phone number')
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('position')
                                        ->label('Position')
                                        ->placeholder('Position or title of the contact person within the company')
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('email')
                                        ->columnSpan(2)
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
                                    ->columns(4)
                                ]),
                            ]),
                        ])
                        ->icon('heroicon-o-device-phone-mobile')
                        ->completedIcon('heroicon-m-device-phone-mobile'),
                    ])->skippable(),
                // Group::make()->schema([
                //     Section::make([
                //         Forms\Components\TextInput::make('name')
                //             ->placeholder('Precision Measurement Specialists, Inc.')
                //             ->required()
                //             ->maxLength(255),
                //         Forms\Components\TextArea::make('address')
                //             ->placeholder("B1 L3 Macaria Business Center. Governor's Dr., Carmona, 4116 Cavite, Philippines")
                //             ->autosize()
                //             ->maxLength(255)    
                //             ->required(),
                //         Forms\Components\TextInput::make('qualifyingSystem')
                //             ->label('Qualifying System')
                //             ->nullable(),
                //         Forms\Components\TextInput::make('certifyingBody')
                //             ->label('Certifying Body')
                //             ->required(),
                //         Forms\Components\DatePicker::make('dateCertified')
                //             ->label('Date Certified')
                //             ->required(),
                //         Forms\Components\TextArea::make('remarks')
                //             ->rows(2)   
                //             ->autosize()
                //             ->nullable(),
                //     ]),
                // ])->columnSpan(2),

                // Group::make()->schema([
                //     Section::make([
                //         Forms\Components\TextInput::make('tin')
                //             ->label('TIN No.')
                //             ->required(),
                //         Forms\Components\TextInput::make('sec')
                //             ->label('SEC Reg no.')
                //             ->nullable(),
                //         Forms\Components\Select::make('vat')
                //             ->label('VAT')
                //             ->options([
                //                 'VAT' => 'VAT',
                //                 'Non-VAT' => 'Non-VAT',
                //             ])
                //             ->required(),
                //         Forms\Components\TextInput::make('wht')
                //             ->label('With Holding Tax')
                //             ->nullable(),
                //         Forms\Components\TextInput::make('businessNature')
                //             ->label('Nature of Business')
                //             ->required(),
                //         Forms\Components\TextInput::make('businessStyle')
                //             ->label('Business Style')
                //             ->required(),
                //     ]),
                // ])->columnSpan(1),

                // Group::make()->schema([
                //     Section::make([ 
                //         PhoneInput::make('phone')
                //             ->defaultCountry('PH')
                //             ->initialCountry('PH')
                //             ->default('+639')
                //             // ->separateDialCode()
                //             ->strictMode()
                //             ->formatAsYouType(false)
                //             ->required(),
                //         PhoneInput::make('landline')
                //             ->nullable()
                //             ->showFlags(false)
                //             ->disallowDropdown()
                //             ->onlyCountries(['AF']),
                //         Forms\Components\TextInput::make('email')
                //             ->placeholder('pmsical@yahoo.com')
                //             ->email()
                //             ->required(),
                //         Forms\Components\TextInput::make('website')
                //             ->placeholder('www.pmsi-cal.com')
                //             ->label('Website')
                //             ->nullable(),
                //         Forms\Components\Select::make('payment')
                //             ->options([
                //                 'Cash on Delivery' => 'Cash on Delivery',
                //                 'Net 7 days' => 'Net 7 days',
                //                 'Net 15 days' => 'Net 15 days',
                //                 'Net 30 days' => 'Net 30 days',
                //                 'Net 60 days' => 'Net 60 days',
                //             ])
                //             ->default('cod')
                //             ->required(),
                //         Forms\Components\Select::make('status')
                //             ->options([
                //                 'Active' => 'Active',
                //                 'Potential' => 'Potential',
                //             ])
                //             ->default('Active')
                //             ->required(),
                //     ])
                // ])->columnSpan(1)
                
                ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
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
                Tables\Columns\TextColumn::make('formatted_mobile')
                    ->label('Mobile')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->iconColor('primary')
                    ->copyable()
                    ->copyMessage('Mobile No. copied')
                    ->html(),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('formatted_telephone')
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
                Tables\Columns\TextColumn::make('wht')
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
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('warning'),
                    Tables\Actions\EditAction::make()
                        ->color('info'),
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
                ->icon('heroicon-o-cog-6-tooth')
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
                                'telephone' => $customer->telephone1, // Replace with the actual field name
                                'mobile' => $customer->mobile1,       // Replace with the actual field name
                                'email' => $customer->email,
                                'contact_persons' => $customer->activeContactPerson->map(function ($contactPerson) {
                                    return [
                                        'name' => $contactPerson->name,
                                        'department' => $contactPerson->department,
                                        'position' => $contactPerson->position,
                                        'contact1' => $contactPerson->contact1,
                                        'contact2' => $contactPerson->contact2,
                                        'email' => $contactPerson->email,
                                    ];
                                })->toArray(),
                                'equipment' => $customer->equipment->map(function ($equipment) {
                                    return [
                                        'equipment_id' => $equipment->equipment_id,
                                        'transaction_id' => $equipment->transaction_id,
                                        'make' => $equipment->make,
                                        'model' => $equipment->model,
                                        'serial' => $equipment->serial,
                                        'description' => $equipment->description,
                                        'calibrationDue' => $equipment->calibrationDue,
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
