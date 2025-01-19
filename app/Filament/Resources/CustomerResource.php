<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\EquipmentRelationManager;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make([
                        Forms\Components\TextInput::make('name')
                            ->placeholder('Precision Measurement Specialists, Inc.')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextArea::make('address')
                            ->placeholder("B1 L3 Macaria Business Center. Governor's Dr., Carmona, 4116 Cavite, Philippines")
                            ->autosize()
                            ->maxLength(255)    
                            ->required(),
                        Forms\Components\TextInput::make('qualifyingSystem')
                            ->label('Qualifying System')
                            ->nullable(),
                        Forms\Components\TextInput::make('certifyingBody')
                            ->label('Certifying Body')
                            ->required(),
                        Forms\Components\DatePicker::make('dateCertified')
                            ->label('Date Certified')
                            ->required(),
                        Forms\Components\TextArea::make('remarks')
                            ->rows(2)   
                            ->autosize()
                            ->nullable(),
                    ]),
                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make([
                        Forms\Components\TextInput::make('tin')
                            ->label('TIN No.')
                            ->nullable(),
                        Forms\Components\TextInput::make('sec')
                            ->label('SEC Reg no.')
                            ->nullable(),
                        Forms\Components\Select::make('vat')
                            ->label('VAT')
                            ->options([
                                'VAT' => 'VAT',
                                'Non-VAT' => 'Non-VAT',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('wht')
                            ->label('With Holding Tax')
                            ->nullable(),
                        Forms\Components\TextInput::make('businessNature')
                            ->label('Nature of Business')
                            ->required(),
                        Forms\Components\TextInput::make('businessStyle')
                            ->label('Business Style')
                            ->required(),
                    ]),
                ])->columnSpan(1),

                Group::make()->schema([
                    Section::make([ 
                        PhoneInput::make('phone')
                            ->defaultCountry('PH')
                            ->initialCountry('PH')
                            ->default('+639')
                            // ->separateDialCode()
                            ->strictMode()
                            ->formatAsYouType(false)
                            ->required(),
                        PhoneInput::make('landline')
                            ->nullable()
                            ->showFlags(false)
                            ->disallowDropdown()
                            ->onlyCountries(['AF']),
                        Forms\Components\TextInput::make('email')
                            ->placeholder('pmsical@yahoo.com')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('website')
                            ->placeholder('www.pmsi-cal.com')
                            ->label('Website')
                            ->nullable(),
                        Forms\Components\Select::make('payment')
                            ->options([
                                'Cash on Delivery' => 'Cash on Delivery',
                                'Net 7 days' => 'Net 7 days',
                                'Net 15 days' => 'Net 15 days',
                                'Net 30 days' => 'Net 30 days',
                                'Net 60 days' => 'Net 60 days',
                            ])
                            ->default('cod')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Active' => 'Active',
                                'Potential' => 'Potential',
                            ])
                            ->default('Active')
                            ->required(),
                    ])
                ])->columnSpan(1)
                
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID No.'),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Customer Name')
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    // ->wrap()
                    ->words(3)
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->iconColor('primary'),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('fax')
                    ->icon('heroicon-o-phone')
                    ->iconColor('primary'),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary')
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500)
                    // ->wrap()
                    ->words(2),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('site')
                    ->label('Website')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('SEC')
                    ->label('SEC Reg No.')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('VAT')
                    ->label('VAT'),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('WTP')
                    ->label('With Holding Tax')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('main_act')
                    ->label('Nature of Business')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('QS')
                    ->label('Qualifying System')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('certifying_body')
                    ->label('Certifying Body')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('date_certified')
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
                Tables\Columns\TextColumn::make('business_system')
                    ->label('Business System')
                    ->toggleable(isToggledHiddenByDefault: true),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('tin')
                    ->label('TIN'),
                    // ->searchable(),
                Tables\Columns\TextColumn::make('acct_created')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Date Created')
                    ->date(),
                    // ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                    ->slideOver(),
                    Tables\Actions\EditAction::make()
                    ->slideOver(),
                    Tables\Actions\DeleteAction::make()
                    ->slideOver(),
                ])
                ->icon('heroicon-o-cog-6-tooth')
                ->tooltip('Options')
                ->color('danger')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EquipmentRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
