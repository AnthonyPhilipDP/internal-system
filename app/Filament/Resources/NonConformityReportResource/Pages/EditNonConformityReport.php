<?php

namespace App\Filament\Resources\NonConformityReportResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\NonConformityReportResource;

class EditNonConformityReport extends EditRecord
{
    protected static string $resource = NonConformityReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FitContent;
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Updated Successfully')
            ->body('The NCF Report data has been modified and saved successfully.')
            ->actions([
                Action::make('downloadPdf')
                    ->button()
                    ->url(fn () => route('downloadPdf', ['reportId' => $this->record->id]), shouldOpenInNewTab: true)
                    ->label('Download PDF')
            ])
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
