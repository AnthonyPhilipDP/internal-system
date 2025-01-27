<?php

namespace App\Filament\Resources\CapabilityResource\Pages;

use App\Filament\Resources\CapabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCapability extends CreateRecord
{
    protected static string $resource = CapabilityResource::class;

    protected static ?string $title = 'Add A New Company Capability';
}
