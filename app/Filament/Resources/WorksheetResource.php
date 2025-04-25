<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Worksheet;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use App\Filament\Resources\WorksheetResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WorksheetResource\RelationManagers;

class WorksheetResource extends Resource
{
    protected static ?string $model = Worksheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'PMSi';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    protected static int $globalSearchResultsLimit = 5;

    public static function form(Form $form): Form
    
    {
        return $form
            ->schema([
                Section::make([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->autocapitalize('words')
                        ->maxLength(255)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('revision')
                        ->label('Revision')
                        ->required()
                        ->autocapitalize('words')
                        ->maxLength(255)
                        ->columnSpan(1),
                ])->columns(2),
                Section::make([
                    FileUpload::make('file')
                        ->previewable(false)
                        // ->panelAspectRatio('2:1')
                        ->uploadingMessage('Uploading worksheet...')
                        ->directory('worksheets')
                        ->disk('public')
                        ->storeFileNamesIn('file_name')
                        ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']),
                ]),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->alignCenter()
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('revision')
                    ->alignCenter()
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->modalIcon('heroicon-o-document-minus')
                    ->modalHeading(fn (Worksheet $record) => 'Remove ' . $record->name)
                    ->modalDescription(fn (Worksheet $record) => 'Are you sure you want to remove the worksheet ' . $record->name . '?')
                    ->modalSubmitActionLabel('Yes')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->icon('heroicon-o-document-minus')
                            ->title('Equipment Removed')
                            ->body('The equipment has been removed successfully.'),
                    ),
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-folder-arrow-down')
                    ->color('info')
                    ->action(function ($record) {
                        if ($record->file) {
                            $filePath = Storage::disk('public')->path($record->file);
                            $fileName = $record->name . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                            return response()->download($filePath, $fileName);
                        } else {
                            Notification::make()
                                ->title('No file available')
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\ForceDeleteAction::make()
                    ->modalIcon('heroicon-o-document-minus')
                    ->modalHeading(fn (Worksheet $record) => 'Remove ' . $record->name . ' permanently')
                    ->modalDescription(fn (Worksheet $record) => 'Are you sure you want to remove the worksheet ' . $record->name . ' permanently?')
                    ->modalSubmitActionLabel('Yes')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->icon('heroicon-o-document-minus')
                            ->title('Equipment Removed Permanently')
                            ->body('The equipment has been permanently removed.'),
                    ),
                Tables\Actions\RestoreAction::make()
                    ->color('primary')
                    ->modalIcon('heroicon-o-document-check')
                    ->modalHeading(fn (Worksheet $record) => 'Bring ' . $record->name . ' back')
                    ->modalDescription(fn (Worksheet $record) => 'Are you sure you want to bring back worksheet ' . $record->name . '?')
                    ->modalSubmitActionLabel('Yes')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->icon('heroicon-o-document-check')
                            ->title('Worksheet Restored')
                            ->body('The worksheet has been restored succesfully.'),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 20, 40])
            // ->paginated([5, 10, 20, 40, 'all'])
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
            'index' => Pages\ListWorksheets::route('/'),
            'create' => Pages\CreateWorksheet::route('/create'),
            'edit' => Pages\EditWorksheet::route('/{record}/edit'),
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
