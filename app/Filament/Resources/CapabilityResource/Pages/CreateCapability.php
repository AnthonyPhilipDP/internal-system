<?php

namespace App\Filament\Resources\CapabilityResource\Pages;

use App\Filament\Resources\CapabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

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
}
