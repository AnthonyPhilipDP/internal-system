<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\EquipmentResource;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

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

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-document-duplicate')
            ->modalHeading('This is a prototype, not yet finished')
            ->modalSubheading('This is a prototype, not yet finished')
            ->modalButton('Confirm')
            // ->tooltip('Duplicate')
            ->color('primary')
            ->action(function(){
                $this->closeActionModal();
                $this->create();
                $this->generateQrCode();
            });
    }
    
    protected function generateQrCode()
    {
        $equipment = $this->record;
        $relativePath = 'admin/equipment/' . $equipment->id . '/edit';
    
        // Generate the QR code with the full URL
        $fullUrl = url($relativePath);
    
        // Create a new QR code instance
        $qrCode = new QrCode($relativePath);
    
        // Create a writer instance
        $writer = new PngWriter();
    
        // Write the QR code to a string
        $result = $writer->write($qrCode);
    
        $fileName = 'qrcodes/equipment_' . $equipment->id . '.png';
        Storage::disk('public')->put($fileName, $result->getString());
    
        // Store only the relative path in the database
        $equipment->update(['qrCodePath' => $relativePath]);
    }
}
