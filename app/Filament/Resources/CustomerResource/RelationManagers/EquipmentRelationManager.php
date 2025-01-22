<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EquipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'equipment';

    // To show automatically in page
    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Equipment Form')->schema([
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
                ])->columnSpan(3),
                Group::make()->schema([
                    Section::make('')->schema([
                        Forms\Components\TextInput::make('lab')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('calType')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('category')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('inspection')
                            ->label('Inspection Findings')
                            ->required()
                            ->maxLength(255),
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
                        ->addActionLabel('Add Another Accessory')
                        ->columns(4),
                    ])
                ])->columnSpan(3)
            ])->columns(6);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('make')
            ->columns([
                Tables\Columns\TextColumn::make('make'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
