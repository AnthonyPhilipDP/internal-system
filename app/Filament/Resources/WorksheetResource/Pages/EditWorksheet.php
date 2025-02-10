<?php

namespace App\Filament\Resources\WorksheetResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\WorksheetResource;

class EditWorksheet extends EditRecord
{
    protected static string $resource = WorksheetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
        // Use the following code to redirect to the previous page after creating a record
        // return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\Action::make('download')
            ->label('Download this Worksheet')
            ->icon('heroicon-o-folder-arrow-down')
            ->color('info')
            ->action(function () {
                $record = $this->record;
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
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Updated Succesfully')
            ->body('The Worksheet data has been modified and saved successfully.');
    }
}
