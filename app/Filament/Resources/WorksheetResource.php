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
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\WorksheetResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WorksheetResource\RelationManagers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class WorksheetResource extends Resource
{
    protected static ?string $model = Worksheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

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
                        FileUpload::make('file')
                        ->previewable(false)
                        ->uploadingMessage('Uploading worksheet...')
                        ->directory('worksheets')
                        ->disk('public')
                        ->required()
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
                Tables\Actions\Action::make('modifyExcel')
                    ->label('Modify Excel')
                    ->action(function ($record) {
                        $filePath = Storage::disk('public')->path($record->file);
                        $spreadsheet = IOFactory::load($filePath);
                
                        // Access the first sheet (index 0)
                        $sheet = $spreadsheet->getSheet(0);
                
                        // Modify cell B3
                        $sheet->setCellValue('A20', 'make');
                        $sheet->setCellValue('b20', 'pmsi');
                
                        // Save the modified file
                        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                        $modifiedFilePath = public_path('modified-file.xlsx');
                        $writer->save($modifiedFilePath);
                
                        // Optionally, download the modified file
                        return response()->download($modifiedFilePath)->deleteFileAfterSend(true);
                }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\Action::make('download')
                ->label('Download File')
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
