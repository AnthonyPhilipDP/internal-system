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
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PriceQuoteResource\Pages;
use App\Filament\Resources\PriceQuoteResource\RelationManagers;

class PriceQuoteResource extends Resource
{
    protected static ?string $model = PriceQuote::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                        Forms\Components\Select::make('customer_id')
                            ->label('To')
                            ->columnSpan(5)
                            ->required()
                            ->searchable()
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
                            ->afterStateUpdated(function (?string $state, callable $get, callable $set): void {
                                if ($state) {
                                    $customer_id = Customer::where('customer_id', $state)->first()?->id;
                                    $contact_person = ContactPerson::where('customer_id', $customer_id)
                                        ->where('isActive', true)
                                        ->first();
                                    if ($contact_person) {
                                        $set('contact_person_identity', $contact_person->identity);
                                        $set('contact_person', $contact_person->name);
                                        $set('customer_fax', $contact_person->contact1);
                                        $set('customer_email', $contact_person->email);
                                        $set('customer_mobile', $contact_person->contact2);

                                        // Set salutation here as well
                                        $prefix = $contact_person->identity === 'female' ? 'Ms.' : 'Mr.';
                                        $set('salutation', "Dear {$prefix} {$contact_person->name}:");
                                    }
                                } else {
                                    $set('contact_person', '');
                                    $set('salutation', null);
                                }
                            }),

                        Forms\Components\Select::make('contact_person_identity')
                            ->label('Prefix')
                            ->inlineLabel(false)
                            ->native(false)
                            ->options([
                                'male' => 'Mr.',
                                'female' => 'Ms.',
                            ])
                            ->columnSpan(2)
                            ->required(),
                        Forms\Components\Select::make('contact_person')
                            ->label('Attention')
                            ->inlineLabel(false)
                            ->columnSpan(3)
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
                                return ContactPerson::where('customer_id', $customer->id)
                                    ->where('isActive', true)
                                    ->pluck('name', 'name')
                                    ->toArray();
                            })
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $customer_id = $get('customer_id');
                                $customer = Customer::where('customer_id', $customer_id)->first();
                                if (!$customer || !$state) {
                                    $set('contact_person_identity', null);
                                    return;
                                }
                                $contactPerson = ContactPerson::where('customer_id', $customer->id)
                                    ->where('name', $state)
                                    ->where('isActive', true)
                                    ->first();
                                if ($contactPerson) {
                                    $set('contact_person_identity', $contactPerson->identity);
                                    $set('customer_fax', $contactPerson->contact1);
                                    $set('customer_email', $contactPerson->email);
                                    $set('customer_mobile', $contactPerson->contact2);

                                    // Set salutation
                                    $prefix = $contactPerson->identity === 'female' ? 'Ms.' : 'Mr.';
                                    $set('salutation', "Dear {$prefix} {$contactPerson->name}:");
                                } else {
                                    $set('contact_person_identity', null);
                                    $set('salutation', null);
                                }
                            }),
                        Forms\Components\TextInput::make('carbon_copy')
                            ->label('CC')
                            ->columnSpan(5),
                        Forms\Components\TextInput::make('subject')
                            ->label('RE')
                            ->columnSpan(5)
                            ->required(),
                        Forms\Components\TextInput::make('salutation')
                            ->label('Salutation')
                            ->columnSpan(2)
                            ->inlineLabel(false)
                            ->required(),
                        Forms\Components\Textarea::make('introduction')
                            ->label('Message')
                            ->columnSpan(3)
                            ->rows(4)
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
                                ->required(),
                        ])->columns(2),
                        Forms\Components\TextInput::make('customer_ref')
                            ->label('Customer Reference'),
                        Forms\Components\TextInput::make('customer_fax')
                            ->label('Customer Fax'),
                        Forms\Components\TextInput::make('pmsi_fax')
                            ->label('PMSI Fax')
                            ->default('(046) 889-0673'),
                        Forms\Components\TextInput::make('customer_email')
                            ->label('Customer Email'),
                        Forms\Components\TextInput::make('customer_mobile')
                            ->label('Customer Mobile'),
                        Forms\Components\TextInput::make('quote_period')
                            ->label('Quote Period')
                            ->default(function () {
                                $start = now();
                                $end = now()->copy()->addDays(30);
                                return $start->format('M d') . ' thru ' . $end->format('M d, Y');
                            }),
                    ])
                ])->columns(2),
                Forms\Components\Repeater::make('equipment_list')
                ->label('Equipment Information')
                ->addActionLabel('Add another row')
                ->relationship()
                ->schema([
                    Forms\Components\TextInput::make('item_number')
                        ->disabled()
                        ->dehydrated()
                        ->default(1)
                        ->required(),
                    Forms\Components\TextInput::make('make')
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                    Forms\Components\TextInput::make('model')
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                    Forms\Components\TextInput::make('description')
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                    Forms\Components\TextInput::make('quantity')
                        ->default(1)
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
                        ->label('Extended Price')
                        ->readOnly()
                        ->default("0.00"),
                    Forms\Components\Textarea::make('comments')
                        ->columnSpan(2)
                        ->rows(1)
                        ->autosize()
                        ->nullable(),
                    Forms\Components\Select::make('transaction_id')
                        ->label('Search')
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
                ])->columns(10)
                ->columnSpanFull()
                // For auto-incrementing item numbers
                ->afterStateUpdated(function ($state, callable $set) {
                    $counter = 1;
                    foreach ($state as $index => $item) {
                        $set("equipment_list.{$index}.item_number", $counter++);
                    }
                }),
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
                Tables\Columns\TextColumn::make('customer_id')
                    ->label('Customer')
                    ->color('primary')
                    ->formatStateUsing(function ($state) {
                        $customer = Customer::where('customer_id', $state)->first();
                        return $customer ? $customer->name : 'Unknown';
                    }),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('printPriceQuote')
                ->label('Print Price Quote')
                ->url(fn ($record) => route('price-quote-manager', ['price_quote_id' => $record->id]))
                ->openUrlInNewTab(),
            ])
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
