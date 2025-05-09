<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OldEquipmentResource\Pages;
use App\Filament\Resources\OldEquipmentResource\RelationManagers;
use App\Models\OldEquipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OldEquipmentResource extends Resource
{
    protected static ?string $model = OldEquipment::class;

    protected static ?string $navigationLabel = 'Equipments (Old)';

    protected static ?string $navigationGroup = 'Tools';

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trans no')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cert no')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date in')
                    ->sortable()
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('po')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('realignpo')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('repairpo')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pr')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('equip id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('make')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial no')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cal date')
                    ->sortable()
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cal due')
                    ->sortable()
                    //not applicable because of the format ->date()
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cod/range')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cal procedure')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('prev condition')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition in')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition out')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('validation')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('temp')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('humidity')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cal interval')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('form no')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ref')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('service')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('comments')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date out')
                    ->sortable()
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('visual insp')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('accessories')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ack receipt no')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('dr_no2')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('standards used')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoiced')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('dr_no3')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('docdr')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('dr_no4')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('num_pages')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority remarks')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('caltype')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('laboratory')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('DocDateReleased')
                    ->sortable()
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('DrNoDocReleased')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('assignedTo')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('PersonReceivedDoc')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                //     Tables\Actions\ForceDeleteBulkAction::make(),
                //     Tables\Actions\RestoreBulkAction::make(),
                // ]),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([5, 10, 20, 40])
            ->extremePaginationLinks()
            ->emptyStateHeading('Old Equipment is Empty')
            ->emptyStateDescription('Contact administrator to import Old Equipment and show them here');
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
            'index' => Pages\ListOldEquipment::route('/'),
            'create' => Pages\CreateOldEquipment::route('/create'),
            'edit' => Pages\EditOldEquipment::route('/{record}/edit'),
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
