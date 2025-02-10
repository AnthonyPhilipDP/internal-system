<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions;
use Spatie\Color\Rgb;
use Filament\Forms\Form;
use App\Models\Equipment;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\EquipmentResource;

class EditEquipment extends EditRecord
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->color(Color::hex(Rgb::fromString('rgb('.Color::Red[500].')')->toHex())),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
       
            // Action::make('save')
            // ->label('Save changes')
            // ->action('save'),
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
            ->body('The Equipment data has been modified and saved successfully.');
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
