<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
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
            ->body('The User data has been modified and saved successfully.');
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }
}
