<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $breadcrumb = "Creation";

    protected static ?string $title = 'Add New Employee';

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
            ->title('Employee Registration Completed')
            ->body('The employee account has been created successfully.');
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }
}
