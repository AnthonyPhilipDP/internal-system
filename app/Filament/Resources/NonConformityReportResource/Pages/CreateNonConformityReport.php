<?php

namespace App\Filament\Resources\NonConformityReportResource\Pages;

use data;
use Filament\Actions;
use App\Models\Equipment;
use App\Models\NcfReport;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use App\Filament\Resources\NonConformityReportResource;

class CreateNonConformityReport extends CreateRecord
{
    protected static string $resource = NonConformityReportResource::class;

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FitContent;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (NcfReport::where('transaction_id', $data['transaction_id'])->exists()) {
            Notification::make()
            ->warning()
            ->title('The report already exists')
            ->body('You cannot create a another NCF report for this equipment.')
            ->persistent()
            ->icon('heroicon-o-x-circle')
            ->send();

            $this->halt();
        }

        // Set the NCF Status to Issued
        Equipment::where('transaction_id', $data['transaction_id'])->update(['ncfReport' => true]);
        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->icon('heroicon-o-document-check')
            ->title('NCF Report Successfully Added')
            ->body('NCF report of the equipment has been saved to the system.')
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
