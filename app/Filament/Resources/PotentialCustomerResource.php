<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PotentialCustomer;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Services\PotentialCustomerTransferService;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PotentialCustomerResource\Pages;
use App\Filament\Resources\PotentialCustomerResource\RelationManagers;

class PotentialCustomerResource extends Resource
{
    protected static ?string $model = PotentialCustomer::class;

    protected static ?string $navigationGroup = 'PMSi';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'bi-person-exclamation';

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
            ])
            ->actions([
                ActionGroup::make([
                    // Tables\Actions\ViewAction::make()
                    //     ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\Action::make('transferToActualCustomer')
                        ->label('Transfer')
                        ->color('info')
                        ->icon('bi-person-up')
                        ->requiresConfirmation()
                        ->modalHeading('Transfer to Actual Customer')
                        ->modalDescription('Are you sure you want to transfer this potential customer to actual customer? This action cannot be undone.')
                        ->modalIcon('bi-person-up')
                        ->modalSubmitActionLabel('Transfer it')
                        ->action(function (PotentialCustomer $record) {
                            $service = new PotentialCustomerTransferService();
                            $service->transfer($record);

                            Notification::make()
                                ->success()
                                ->icon('bi-person-check')
                                ->title('Transferred')
                                ->body('Potential customer has been transferred to actual customers.')
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->modalIcon('heroicon-o-user-minus')
                        ->modalHeading(fn (PotentialCustomer $record) => 'Remove ' . $record->name)
                        ->modalDescription(fn (PotentialCustomer $record) => 'Are you sure you want to remove ' . $record->name . ' as our customer?')
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
                        ->modalHeading(fn (PotentialCustomer $record) => 'Remove ' . $record->name . ' permanently')
                        ->modalDescription(fn (PotentialCustomer $record) => 'Are you sure you want to remove ' . $record->name . ' permanently as our customer?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-user-minus')
                                ->title('Customer Removed Permanently')
                                ->body('The customer has been permanently removed.'),
                        ),
                    Tables\Actions\RestoreAction::make()
                        ->after(function (PotentialCustomer $record) {
                            $record->transferred_at = null;
                            $record->save();
                        })
                        ->color('primary')
                        ->modalIcon('heroicon-o-user-plus')
                        ->modalHeading(fn (PotentialCustomer $record) => 'Bring ' . $record->name . ' back')
                        ->modalDescription(fn (PotentialCustomer $record) => 'Are you sure you want to bring back ' . $record->name . ' as our customer?')
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
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
            'index' => Pages\ListPotentialCustomers::route('/'),
            'create' => Pages\CreatePotentialCustomer::route('/create'),
            'edit' => Pages\EditPotentialCustomer::route('/{record}/edit'),
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
