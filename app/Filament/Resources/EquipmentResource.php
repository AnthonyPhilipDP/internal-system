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
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EquipmentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EquipmentResource\RelationManagers;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Customer Form')->schema([
                        Forms\Components\Select::make('customer_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('customer', 'name'),
                        Forms\Components\TextInput::make('manufacturer')
                            ->required()    
                            ->maxLength(255),

                        Forms\Components\TextInput::make('model')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('serial')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->maxLength(255),
                    ]),
                ])->columnSpan(2),
                Group::make()->schema([
                    Section::make('Customer Form')->schema([
                        Forms\Components\TextInput::make('inspection')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('lab')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('calType')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('category')
                            ->required()
                            ->maxLength(255),
                    ]),
                ])->columnSpan(1),
                Group::make()->schema([
                    Section::make('Order Items')->schema([
                        Forms\Components\Repeater::make('accessory')
                            ->relationship()
                            ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required(),
                            Forms\Components\TextInput::make('quantity')
                                ->required(),
                        ]),
                    ])
                ])->columnSpan(1)
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_id')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('manufacturer')
                ->alignCenter()
                    ->label('Manufacturer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inspection')
                    ->alignCenter()
                    ->label('Inspection Findings')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lab')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('calType')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->alignCenter()
                    ->searchable(),
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->color(Color::hex(Rgb::fromString('rgb('.Color::Gray[900].')')->toHex())),
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
}
