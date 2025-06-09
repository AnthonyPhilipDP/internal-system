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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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

                                // Count the existing ClientExclusive records for this customer
                                $count = ClientExclusive::where('customer_id', $state)->count() + 1;

                                // Construct the exclusive_id
                                $exclusiveId = $state . '-' . $count;
                                $set('exclusive_id', $exclusiveId);
                            }
                        } else {
                            $set('customerAddress', '');
                            $set('exclusive_id', '');
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
                Forms\Components\TextInput::make('exclusive_id')
                    ->label('Exclusive ID')  
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Name')  
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->label('Address')  
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer ID')
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
            ])
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
