<?php

namespace App\Filament\Resources\PotentialCustomerResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PotentialCustomerResource;

class CreatePotentialCustomer extends CreateRecord
{
    protected static string $resource = PotentialCustomerResource::class;

    protected static ?string $breadcrumb = "Creation";

    protected static ?string $title = "Add New Potential Customer";

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
        // Use the following code to redirect to the previous page after creating a record
        // return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->icon('heroicon-o-user-plus')
            ->title('Potential Customer Successfully Added')
            ->body('New Potential Customer has been added to the system.');
    }
}
