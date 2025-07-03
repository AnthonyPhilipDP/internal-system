<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClientExclusive;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClientExclusiveResource\Pages;
use App\Filament\Resources\ClientExclusiveResource\RelationManagers;

class ClientExclusiveResource extends Resource
{
    protected static ?string $model = ClientExclusive::class;

    protected static ?string $navigationIcon = 'bi-people';

    protected static ?string $navigationGroup = 'PMSi';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                ->schema([
                    Forms\Components\Select::make('customer_id')
                    ->label('Client Exclusive of')
                    ->columnSpan(4)
                    ->searchable()
                    ->required()
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

                                // Count the existing ClientExclusive records for this customer
                                $count = ClientExclusive::where('customer_id', $state)->count() + 1;

                                // Construct the exclusive_id
                                $customerId = $state;
                                $prefix = substr($customerId, 0, 3); // Extract the first 3 digits
                                $suffix = substr($customerId, 3); // Extract the remaining digits
                                $exclusiveId = $prefix . '-' . $suffix . '-' . $count;
                                $set('exclusive_id', $exclusiveId);
                            }
                        } else {
                            $set('customerAddress', '');
                            $set('exclusive_id', '');
                        }
                    }),
                Forms\Components\TextInput::make('exclusive_id')
                    ->label('Client Exclusive ID')  
                    ->readonly()
                    ->maxLength(255)
                    ->columnSpan(2),
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
                    ->autosize()
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('name')
                    ->label('Client Name')  
                    ->maxLength(255)
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('address')
                    ->label('Client Address')  
                    ->maxLength(255)
                    ->autosize()
                    ->required()
                    ->columnSpanFull(),
                ])
                ->columns(6)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Client Exclusive of')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exclusive_id')
                    ->label('Customer ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Client Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->sortable(),
            ])->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListClientExclusives::route('/'),
            'create' => Pages\CreateClientExclusive::route('/create'),
            'edit' => Pages\EditClientExclusive::route('/{record}/edit'),
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
