<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions;
use Spatie\Color\Rgb;
use Filament\Forms\Form;
use App\Models\Equipment;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Group;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\DatePicker;
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

    public function form(Form $form): Form
    {
        return parent::form($form)->schema($this->getFormSchema());
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Equipment Form')->schema([
                Group::make()->schema([
                    Section::make('')->schema([
                        Select::make('customer_id')
                            ->required()
                            ->relationship('customer', 'name')
                            ->searchable(['name', 'id'])
                            ->preload()
                            ->prefixIcon('heroicon-o-user')
                            ->prefixIconColor('primary'),
                        TextInput::make('equipment_id')
                            ->required()  
                            ->label('Equipment Identification')  
                            ->maxLength(255),
                        TextInput::make('manufacturer')
                            ->required()    
                            ->maxLength(255),
                        TextInput::make('model')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('serial')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('description')
                            ->required()
                            ->maxLength(255),
                    ]),
                ])
                ->columnSpan(2),
                Group::make()->schema([
                    Section::make('')->schema([
                        Select::make('lab')
                        ->label('Laboratory')
                        ->options([
                            'electrical' => 'Electrical',
                            'physical' => 'Physical',
                            'repair' => 'Repair',
                        ])
                        ->default('electrical')
                        ->native(false)
                        ->searchable()
                        ->required(),
                        Select::make('calType')
                        ->label('Calibration Type')
                        ->options([
                            'iso' => 'ISO 17025',
                            'ansi' => 'ANSI Z540',
                            'milstd' => 'Military Standard',
                        ])
                        ->default('iso')
                        ->native(false)
                        ->searchable()
                        ->required(),
                        Select::make('category')
                        ->label('Category')
                        ->options([
                            'mass' => 'Mass',
                            'force' => 'Force',
                            'torque' => 'Torque',
                            'vacuum' => 'Vacuum',
                            'pressure' => 'Pressure',
                            'humidity' => 'Humidity',
                            'electrical' => 'Electrical',
                            'dimensional' => 'Dimensional',
                            'temperature' => 'Temperature',
                            'conductivity' => 'Conductivity',
                            'pcr' => 'pH / Conductivity / Resistivity',
                        ])
                        ->default('dimensional')
                        ->native(false)
                        ->searchable()
                        ->required(),
                        Select::make('inspection')
                            ->validationAttribute('inspection findings')
                            ->label('Inspection Findings')
                            ->multiple()
                            ->nullable()
                            ->options([
                                'no visible damage' => 'No Visible Damage',
                                'scratches' => 'Scratches',
                                'cracks' => 'Cracks',
                                'grime' => 'Grime',
                                'dents' => 'Dents',
                                'rust' => 'Rust',
                                'bent' => 'Bent',
                            ]),
                        DatePicker::make('inDate')
                            ->label('Date Received')
                            ->default(now())
                            ->required(),
                    ]),
                ])
                ->columnSpan(1),
                Group::make()->schema([
                    Section::make('')->schema([
                        Toggle::make('sameToggle')
                            ->label('Same')
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-bolt')
                            ->onColor('success')
                            ->offColor('danger')
                            ->reactive()
                            ->afterStateUpdated(function (bool $state, callable $get, callable $set): void {
                                $originalArId = $get('original_ar_id');
                                $currentArId = $get('ar_id');
                                // If toggle true, decrement by one; otherwise, use original ar_id.
                                $newValue = $state ? ((int)$originalArId - 1) : $originalArId;
                                $set('ar_id', (string)$newValue);
                            }),
                        // TextInput for ar_id: shows computed value and updates on hydration.
                        TextInput::make('ar_id')
                            ->label('Acknowledgement Receipt No.')
                            ->readonly()
                            ->reactive()
                            ->prefix('401 -')
                            ->afterStateHydrated(function (?string $state, callable $get, callable $set): void {
                                $currentArId = $state ?? '0';
                                $set('original_ar_id', $currentArId); // Store the original ar_id
                                $toggle = $get('sameToggle');
                                $newValue = $toggle ? ((int)$currentArId - 1) : $currentArId;
                                $set('ar_id', (string)$newValue);
                            })
                            ->maxLength(255),
                        Repeater::make('accessory')
                            ->relationship()
                            ->schema([
                            TextInput::make('name')
                                ->columnSpan(2),
                            TextInput::make('quantity')
                                ->numeric()
                                ->columnSpan(2),
                        ])
                        ->reorderable()
                        ->reorderableWithButtons()
                        ->reorderableWithDragAndDrop()
                        ->collapsible()
                        ->addActionLabel(function (callable $get) {
                            $accessories = $get('accessory');
                            return empty($accessories) ? 'Add Accessory' : 'Add Another Accessory';
                        })
                        ->defaultItems(0)
                        ->columns(4),
                    ]),
                ])
                ->columnSpan(1),
            ])
            ->columns(4),
        ];
    }
}
