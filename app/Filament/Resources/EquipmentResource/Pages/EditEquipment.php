<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\EquipmentResource;

class EditEquipment extends EditRecord
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // public function form(Form $form): Form
    // {
    //     return parent::form($form)->schema($this->getFormSchema());
    // }

    // protected function getFormSchema(): array
    // {
    //     return [
    //         TextInput::make('make')
    //             ->required(),
    //         Select::make('customer_id')
    //             ->required()
    //             ->searchable()
    //             ->preload()
    //             ->relationship('customer', 'name'),
    //     ];
    // }
}
