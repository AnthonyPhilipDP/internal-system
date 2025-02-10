<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\EquipmentResource;

class CreateEquipment extends CreateRecord
{
    protected static string $resource = EquipmentResource::class;

    protected static ?string $breadcrumb = "Creation";
    
    protected static ?string $title = "Add New Equipment";
    
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
            ->icon('heroicon-o-cube')
            ->title('Equipment Successfully Added')
            ->body('New Equipment has been added to the system.');
    }
}
