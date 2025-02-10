<?php

namespace App\Filament\Resources\WorksheetResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\WorksheetResource;

class CreateWorksheet extends CreateRecord
{
    protected static string $resource = WorksheetResource::class;

    protected static ?string $breadcrumb = "Creation";

    protected static ?string $title = "Add New Worksheet";

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
            ->icon('heroicon-o-document-check')
            ->title('Worksheet Successfully Added')
            ->body('New Worksheet has been added to the system.');
    }
}
