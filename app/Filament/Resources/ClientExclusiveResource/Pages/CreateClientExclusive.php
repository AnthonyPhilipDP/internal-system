<?php

namespace App\Filament\Resources\ClientExclusiveResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ClientExclusiveResource;

class CreateClientExclusive extends CreateRecord
{
    protected static string $resource = ClientExclusiveResource::class;

    protected static ?string $breadcrumb = "Creation";

    protected static ?string $title = "Add New Client Exclusive";

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->icon('heroicon-o-user-plus')
            ->title('Client Exclusive Successfully Added')
            ->body('New Customer Client Exclusive has been added to the system.');
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }
}
