<?php

namespace App\Filament\Resources\PotentialCustomerResource\Pages;

use Filament\Actions;
use App\Models\PotentialCustomer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Services\PotentialCustomerTransferService;
use App\Filament\Resources\PotentialCustomerResource;

class EditPotentialCustomer extends EditRecord
{
    protected static string $resource = PotentialCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make()
                ->after(function (PotentialCustomer $record) {
                    $record->transferred_at = null;
                    $record->save();
                }),
            Actions\Action::make('transferToActualCustomer')
                ->label('Transfer to Actual Customer')
                ->color('info')
                ->icon('bi-person-up')
                ->requiresConfirmation()
                ->modalHeading('Transfer to Actual Customer')
                ->modalDescription('Are you sure you want to transfer this potential customer to actual customer? This action cannot be undone.')
                ->modalIcon('bi-person-up')
                ->modalSubmitActionLabel('Transfer it')
                ->action(function (PotentialCustomer $record) {
                    $service = new PotentialCustomerTransferService();
                    $service->transfer($record);

                    Notification::make()
                        ->success()
                        ->icon('bi-person-check')
                        ->title('Transferred')
                        ->body('Potential customer has been transferred to actual customers.')
                        ->send();

                    return redirect(PotentialCustomerResource::getUrl('index'));
                })
                ->visible(fn ($record) => !$record->transferred_at),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
        // Use the following code to redirect to the previous page after creating a record
        // return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Updated Succesfully')
            ->body('The Potential Customer data has been modified and saved successfully.');
    }
}
