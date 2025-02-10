<?php

namespace App\Filament\Resources\CapabilityResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CapabilityResource;

class CreateCapability extends CreateRecord
{
    protected static string $resource = CapabilityResource::class;

    protected static ?string $breadcrumb = "Creation";

    protected static ?string $title = 'Add New Company Capability';

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
            ->icon('heroicon-o-clipboard-document-check')
            ->title('Capability Successfully Added')
            ->body('New Capability has been added to the system.');
    }
    
}
