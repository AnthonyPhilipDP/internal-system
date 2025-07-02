<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Models\Equipment;
use App\Models\PriceQuote;
use Filament\Tables\Table;
use App\Models\ContactPerson;
use Filament\Resources\Resource;
use App\Models\PotentialCustomer;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Enums\Alignment;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Models\PotentialCustomerContactPerson;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PriceQuoteResource\Pages;
use App\Filament\Resources\PriceQuoteResource\RelationManagers;

class PriceQuoteResource extends Resource
{
    protected static ?string $navigationGroup = 'PMSi';

    protected static ?string $model = PriceQuote::class;

    protected static ?string $navigationIcon = 'bi-file-earmark-spreadsheet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                ->compact()
                ->inlineLabel()
                ->schema([
                    Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\ToggleButtons::make('use_actual_customer')
                        ->label('Choose Customer')
                            ->options([
                                '0' => 'Potential Customer',
                                '1' => 'Actual Customer',
                            ])
                            ->icons([
                                '0' => 'bi-person-exclamation',
                                '1' => 'bi-person-check',
                            ])
                            ->default(0)
                            ->inline()
                            ->columnSpan(5),
                        Forms\Components\Select::make('customer_id')
                            ->label('To')
                            ->columnSpan(5)
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->options(function (callable $get) {
                                $useActual = $get('use_actual_customer');
                                if ($useActual == '0') {
                                    // Potential Customer
                                    return PotentialCustomer::query()
                                        ->latest('created_at')
                                        ->pluck('name', 'customer_id')
                                        ->toArray();
                                } else {
                                    // Actual Customer
                                    return Customer::query()
                                        ->latest('created_at')
                                        ->pluck('name', 'customer_id')
                                        ->toArray();
                                }
                            })
                            ->getSearchResultsUsing(function (string $search, callable $get) {
                                $useActual = $get('use_actual_customer');
                                if ($useActual == '0') {
                                    return PotentialCustomer::query()
                                        ->where(function ($query) use ($search) {
                                            $query->where('name', 'like', "%{$search}%")
                                                ->orWhere('nickname', 'like', "%{$search}%")
                                                ->orWhere('customer_id', 'like', "%{$search}%");
                                        })
                                        ->pluck('name', 'customer_id')
                                        ->toArray();
                                } else {
                                    return Customer::query()
                                        ->where(function ($query) use ($search) {
                                            $query->where('name', 'like', "%{$search}%")
                                                ->orWhere('nickname', 'like', "%{$search}%")
                                                ->orWhere('customer_id', 'like', "%{$search}%");
                                        })
                                        ->pluck('name', 'customer_id')
                                        ->toArray();
                                }
                            })
                            ->getOptionLabelUsing(function ($value, callable $get) {
                                $useActual = $get('use_actual_customer');
                                if ($useActual == '0') {
                                    $customer = PotentialCustomer::find($value);
                                    return $customer ? $customer->name : null;
                                } else {
                                    $customer = Customer::where('customer_id', $value)->first();
                                    return $customer ? $customer->name : null;
                                }
                            })
                            ->afterStateUpdated(function (?string $state, callable $get, callable $set): void {
                                $useActual = $get('use_actual_customer');
                                if ($state) {
                                    if ($useActual == '0') {
                                        $customer_id = PotentialCustomer::where('customer_id', $state)->first()?->id;
                                        $contact_person = PotentialCustomerContactPerson::where('potential_customer_id', $customer_id)
                                            ->where('isActive', true)
                                            ->first();
                                    }
                                    else {
                                        $customer_id = Customer::where('customer_id', $state)->first()?->customer_id;
                                        $contact_person = ContactPerson::where('customer_id', $customer_id)
                                            ->where('isActive', true)
                                            ->first();
                                    }

                                    if ($contact_person) {
                                        if($contact_person->identity === 'female') {
                                            $prefix = 'Ms.';
                                        }
                                        elseif($contact_person->identity === 'male') {
                                            $prefix = 'Mr.';
                                        }
                                        else {
                                            $prefix = null;
                                        }
                                        $set('contact_person', "{$prefix} {$contact_person->name}");
                                        $set('customer_fax', $contact_person->contact1);
                                        $set('customer_email', $contact_person->email);
                                        $set('customer_mobile', $contact_person->contact2);

                                        // Set salutation here as well
                                        $set('salutation', "Dear {$prefix} {$contact_person->name}:");
                                    }
                                } else {
                                    $set('contact_person', '');
                                    $set('salutation', null);
                                }
                            }),

                        Forms\Components\Select::make('contact_person')
                            ->label('Attention')
                            ->columnSpan(5)
                            ->markAsRequired(false)
                            ->required()
                            ->native(false)
                            ->reactive()
                            ->options(function (callable $get) {
                                $customer_id = $get('customer_id');
                                if (!$customer_id) {
                                    return [];
                                }
                                $customer = Customer::where('customer_id', $customer_id)->first();
                                if (!$customer) {
                                    return [];
                                }
                                return ContactPerson::where('customer_id', $customer_id)
                                    ->where('isActive', true)
                                    ->get()
                                    ->mapWithKeys(function ($person) {
                                        if($person->identity === 'female') {
                                            $prefix = 'Ms.';
                                        }
                                        elseif($person->identity === 'male') {
                                            $prefix = 'Mr.';
                                        }
                                        else {
                                            $prefix = null;
                                        }
                                        $label = "{$prefix} {$person->name}";
                                        return [$label => $label];
                                    })
                                    ->toArray();
                            })
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $customer_id = $get('customer_id');
                                $contactPerson = ContactPerson::where('customer_id', $customer_id)
                                    ->where('name', $state)
                                    ->where('isActive', true)
                                    ->first();
                                if ($contactPerson) {
                                    $set('customer_fax', $contactPerson->contact1);
                                    $set('customer_email', $contactPerson->email);
                                    $set('customer_mobile', $contactPerson->contact2);

                                    // Set salutation
                                    if($contactPerson->identity === 'female') {
                                        $prefix = 'Ms.';
                                    }
                                    elseif($contactPerson->identity === 'male') {
                                        $prefix = 'Mr.';
                                    }
                                    else {
                                        $prefix = null;
                                    }
                                    $set('salutation', "Dear {$prefix} {$contactPerson->name}:");
                                } else {
                                    $set('salutation', null);
                                }
                            }),
                        Forms\Components\Select::make('carbon_copy')
                            ->label('CC')
                            ->native(false)
                            ->options(function (callable $get) {
                                $customer_id = $get('customer_id');
                                $useActual = $get('use_actual_customer');
                                if (!$customer_id) {
                                    return [];
                                }
                                if ($useActual == '0') {
                                    $customer = PotentialCustomer::where('customer_id', $customer_id)->first();
                                }
                                $customer = Customer::where('customer_id', $customer_id)->first();
                                if (!$customer) {
                                    return [];
                                }
                                if ($useActual == '0') {
                                    return PotentialCustomerContactPerson::where('potential_customer_id', $customer->id)
                                        ->where('isActive', true)
                                        ->get()
                                        ->mapWithKeys(function ($person) {
                                            if($person->identity === 'female') {
                                                $prefix = 'Ms.';
                                            }
                                            elseif($person->identity === 'male') {
                                                $prefix = 'Mr.';
                                            }
                                            else {
                                                $prefix = null;
                                            }
                                            $label = "{$prefix} {$person->name}";
                                            return [$label => $label];
                                        })
                                        ->toArray();
                                }
                                return ContactPerson::where('customer_id', $customer_id)
                                    ->where('isActive', true)
                                    ->get()
                                    ->mapWithKeys(function ($person) {
                                        if($person->identity === 'female') {
                                            $prefix = 'Ms.';
                                        }
                                        elseif($person->identity === 'male') {
                                            $prefix = 'Mr.';
                                        }
                                        else {
                                            $prefix = null;
                                        }
                                        $label = "{$prefix} {$person->name}";
                                        return [$label => $label];
                                    })
                                    ->toArray();
                            })
                            ->columnSpan(5),
                        Forms\Components\TextInput::make('subject')
                            ->label('RE')
                            ->default('Price Quotation')
                            ->columnSpan(5)
                            ->required(),
                        Forms\Components\TextInput::make('salutation')
                            ->label('Salutation')
                            ->columnSpan(2)
                            ->autocomplete(false)
                            ->inlineLabel(false)
                            ->required(),
                        Forms\Components\Textarea::make('introduction')
                            ->label('Message')
                            ->columnSpan(3)
                            ->rows(3)
                            ->autosize()
                            ->inlineLabel(false)
                            ->default('Price quoted is for IN-HOUSE (PMSi Facility) calibration only and does not include cost for repair or realignment if required. Unless otherwise specified in this price quote.')
                            ->required(),
                    ])->columns(5),
                    Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\DatePicker::make('price_quote_date')
                                ->label('PQ Date')
                                ->required()
                                ->default(now()),
                            Forms\Components\TextInput::make('price_quote_number')
                                ->label('PQ Number')
                                ->prefix('20-')
                                ->default(fn () => (PriceQuote::max('price_quote_number') ?? 18900) + 1)
                                ->disabled(),
                        ])->columns(2),
                        Forms\Components\TextInput::make('customer_ref')
                            ->autocomplete(false)
                            ->label('Customer Reference'),
                        Forms\Components\TextInput::make('customer_fax')
                            ->autocomplete(false)
                            ->label('Customer Fax'),
                        Forms\Components\TextInput::make('pmsi_fax')
                            ->autocomplete(false)
                            ->label('PMSI Fax')
                            ->default('(046) 889-0673'),
                        Forms\Components\TextInput::make('customer_email')
                            ->autocomplete(false)
                            ->autocomplete(false)
                            ->label('Customer Email'),
                        Forms\Components\TextInput::make('customer_mobile')
                            ->autocomplete(false)
                            ->label('Customer Mobile'),
                        Forms\Components\TextInput::make('quote_period')
                            ->label('Quote Period')
                            ->readOnly()
                            ->default(function () {
                                $start = now();
                                $end = now()->copy()->addDays(30);
                                return $start->format('M d') . ' thru ' . $end->format('M d, Y');
                            }),
                    ])
                ])->columns(2),
                Forms\Components\Repeater::make('equipment_list')
                ->label('Equipment Information')
                ->helperText('Kindly fill out the blue shaded fields manually.')
                ->addActionLabel('Add another row')
                ->relationship()
                ->schema([
                    Forms\Components\TextInput::make('item_number')
                        ->extraAttributes([
                            'style' => 'background-color: #f5f9ff'
                        ])
                        ->label('Item #')
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('make')
                        ->disabled()
                        ->dehydrated()
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('model')
                        ->disabled()
                        ->dehydrated()
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('description')
                        ->required()
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('quantity')
                        ->numeric()
                        ->extraAttributes([
                            'style' => 'background-color: #f5f9ff'
                        ])
                        ->columnSpan(2)
                        ->default(0)
                        ->required()
                        ->reactive()
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $lineTotal = $state * ($get('unit_price') ?? 0);
                            $set('line_total', $lineTotal);

                            // Update subtotal
                            $equipmentList = $get('../../equipment_list') ?? [];
                            $subtotal = 0;
                            foreach ($equipmentList as $item) {
                                $subtotal += floatval($item['line_total'] ?? 0);
                            }
                            $set('../../subtotal', number_format($subtotal, 2, '.', ''));

                            // Compute VAT and Total based on toggle
                            $vatEnabled = $get('../../vat');
                            $vatAmount = $vatEnabled ? ($subtotal * 0.12) : 0;
                            $set('../../vat_amount', number_format($vatAmount, 2, '.', ''));
                            $set('../../total', number_format($subtotal + $vatAmount, 2, '.', ''));
                        }),
                    Forms\Components\TextInput::make('unit_price')
                        ->label('Unit Price')
                        ->extraAttributes([
                            'style' => 'background-color: #f5f9ff'
                        ])
                        ->numeric()
                        ->columnSpan(2)
                        ->default("0.00")
                        ->required()
                        ->reactive()
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $lineTotal = $state * ($get('quantity') ?? 0);
                            $set('line_total', $lineTotal);

                            // Update subtotal
                            $equipmentList = $get('../../equipment_list') ?? [];
                            $subtotal = 0;
                            foreach ($equipmentList as $item) {
                                $subtotal += floatval($item['line_total'] ?? 0);
                            }
                            $set('../../subtotal', number_format($subtotal, 2, '.', ''));

                            // Compute VAT and Total based on toggle
                            $vatEnabled = $get('../../vat');
                            $vatAmount = $vatEnabled ? ($subtotal * 0.12) : 0;
                            $set('../../vat_amount', number_format($vatAmount, 2, '.', ''));
                            $set('../../total', number_format($subtotal + $vatAmount, 2, '.', ''));
                        }),
                    Forms\Components\TextInput::make('line_total')
                        ->required()
                        ->columnSpan(2)
                        ->label('Extended Price')
                        ->readOnly()
                        ->default("0.00"),
                    Forms\Components\Select::make('transaction_id')
                        ->label('Search')
                        ->columnSpan(2)
                        ->getSearchResultsUsing(function (string $search) {
                            return Equipment::query()
                                ->where('transaction_id', 'like', "%{$search}%")
                                ->orWhere('equipment_id', 'like', "%{$search}%") // Add orWhere to search another field
                                ->limit(10)
                                ->pluck('transaction_id', 'transaction_id')
                                ->toArray();
                        })
                        ->searchable()
                        ->reactive()
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, callable $set) {
                            $equipment = Equipment::where('transaction_id', $state)->first();

                            if ($equipment) {
                                $set('make', $equipment->make);
                                $set('model', $equipment->model);
                                $set('description', $equipment->description);
                            } else {
                                $set('make', null);
                                $set('model', null);
                                $set('description', null);
                            }
                        }),
                ])->columns(16)
                ->columnSpanFull(),
                Forms\Components\Section::make('Price Summary')
                ->compact()
                ->columns(4)
                ->schema([
                    Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label('Note')
                            ->rows(9)
                            ->required(),  
                    ])->columnSpan(3),
                    Forms\Components\Group::make()
                    ->schema([
                    Forms\Components\TextInput::make('subtotal')
                        ->label('Subtotal')
                        ->columnSpan(3)
                        ->readOnly()
                        ->default("0.00")->extraInputAttributes([
                            'class' => 'text-center'
                        ]),
                    Forms\Components\Toggle::make('vat')
                        ->label('VAT')
                        ->columnSpan(1)
                        ->inline(false)
                        ->required()
                        ->default(true)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $subtotal = floatval($get('subtotal') ?? 0);
                            $vatAmount = $state ? ($subtotal * 0.12) : 0;
                            $set('vat_amount', number_format($vatAmount, 2, '.', ''));
                            $set('total', number_format($subtotal + $vatAmount, 2, '.', ''));
                        }),
                    Forms\Components\TextInput::make('vat_amount')
                        ->label('VAT Amount')
                        ->columnSpan(2)
                        ->required()
                        ->default("0.00")
                        ->readOnly()
                        ->extraInputAttributes([
                            'class' => 'text-center'
                        ]),
                    Forms\Components\TextInput::make('total')
                        ->label('Total')
                        ->columnSpan(3)
                        ->required()
                        ->default("0.00")
                        ->readOnly()
                        ->extraInputAttributes([
                            'class' => 'text-center'
                        ])
                        ->dehydrated(),
                    ])
                    ->columns(3)
                    ->columnSpan(1)
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('price_quote_date')
                    ->label('Date')
                    ->date('M d, Y'),
                Tables\Columns\TextColumn::make('price_quote_number')
                    ->label('PQ #'),
                Tables\Columns\TextColumn::make('customer_id')
                    ->label('Customer')
                    ->color('primary')
                    ->formatStateUsing(function ($state) {
                        $customer = Customer::where('customer_id', $state)->first();
                        return $customer ? $customer->name : 'Unknown';
                    }),
                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Contact Person'),
                Tables\Columns\TextColumn::make('carbon_copy')
                    ->label('CC')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('customer_ref')
                    ->label('Customer Ref'),
                Tables\Columns\TextColumn::make('customer_fax')
                    ->label('Customer Fax'),
                Tables\Columns\TextColumn::make('customer_email')
                    ->label('Customer Email'),
                Tables\Columns\TextColumn::make('customer_mobile')
                    ->label('Customer Mobile'),
                Tables\Columns\TextColumn::make('quote_period')
                    ->label('Quote Period'),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal'),
                Tables\Columns\IconColumn::make('vat')
                    ->label('VAT')
                    ->icon(fn (string $state): string => match ($state) {
                        '1' => 'heroicon-o-check-circle',
                        '0' => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('vat_amount')
                    ->label('VAT Amount'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total'),
            ])->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('replicateWithEquipment')
                    ->label('Replicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalSubmitActionLabel('Replicate')
                    ->modalHeading('Replicate Price Quote')
                    ->modalDescription('This will replicate the price quote with new price quote number')
                    ->modalIcon('heroicon-o-document-duplicate')
                    ->modalIconColor('info')
                    ->action(function (PriceQuote $record) {
                        // Replicate the PriceQuote
                        $newQuote = $record->replicate();
                        $maxNumber = PriceQuote::max('price_quote_number') ?? 18900;
                        $newQuote->price_quote_number = $maxNumber + 1;
                        $newQuote->save();

                        // Replicate related equipment
                        foreach ($record->equipment_list as $equipment) {
                            $newEquipment = $equipment->replicate();
                            $newEquipment->price_quote_id = $newQuote->id;
                            $newEquipment->save();
                        }

                        Notification::make()
                            ->success()
                            ->icon('heroicon-o-document-duplicate')
                            ->title('Price Quotation Replicated')
                            ->body('The price quotation and its equipment have been replicated successfully with a new price quote number.')
                            ->send();
                    })
                ])
                ->icon('heroicon-o-ellipsis-horizontal-circle')
                ->tooltip('Options')
                ->color('danger'),
                Tables\Actions\Action::make('printPriceQuote')
                ->label('Print')
                ->tooltip('Print Price Quote')
                ->icon('bi-printer-fill')
                ->color('info')
                ->url(fn ($record) => route('price-quote-manager', ['price_quote_id' => $record->id]))
                ->openUrlInNewTab(),
            ], position: ActionsPosition::BeforeColumns)
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
            'index' => Pages\ListPriceQuotes::route('/'),
            'create' => Pages\CreatePriceQuote::route('/create'),
            'edit' => Pages\EditPriceQuote::route('/{record}/edit'),
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
