<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Capability;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CapabilityResource\Pages;
use App\Filament\Resources\CapabilityResource\RelationManagers;

class CapabilityResource extends Resource
{
    protected static ?string $model = Capability::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'PMSi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make([
                        Forms\Components\TextInput::make('name')
                                ->required()
                                ->autocapitalize('words')
                                ->maxLength(255),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->alignCenter()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(), 
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalIcon('heroicon-o-document-minus')
                    ->modalHeading(fn (Capability $record) => 'Remove ' . $record->name)
                    ->modalDescription(fn (Capability $record) => 'Are you sure that we are not capable of calibrating ' . $record->name . '?')
                    ->modalSubmitActionLabel('Yes')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->icon('heroicon-o-document-minus')
                            ->title('Capability Removed')
                            ->body('The capability has been removed successfully.'),
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->modalIcon('heroicon-o-document-minus')
                    ->modalHeading(fn (Capability $record) => 'Remove ' . $record->name . ' permanently')
                    ->modalDescription(fn (Capability $record) => 'Are you sure you want to remove ' . $record->name . ' permanently?')
                    ->modalSubmitActionLabel('Yes')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->icon('heroicon-o-document-minus')
                            ->title('Capability Removed Permanently')
                            ->body('The capability has been permanently removed.'),
                    ),
                Tables\Actions\RestoreAction::make()
                    ->color('primary')
                    ->modalIcon('heroicon-o-document-check')
                    ->modalHeading(fn (Capability $record) => 'Bring ' . $record->name . ' back')
                    ->modalDescription(fn (Capability $record) => 'Are we capable of calibrating ' . $record->name . ' now?')
                    ->modalSubmitActionLabel('Yes')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->icon('heroicon-o-document-check')
                            ->title('Capability Restored')
                            ->body('The capability has been restored succesfully.'),
                    ),
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
            'index' => Pages\ListCapabilities::route('/'),
            'create' => Pages\CreateCapability::route('/create'),
            'edit' => Pages\EditCapability::route('/{record}/edit'),
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
