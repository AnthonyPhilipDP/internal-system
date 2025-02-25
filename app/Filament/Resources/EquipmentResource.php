<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Spatie\Color\Rgb;
use Filament\Forms\Set;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Models\Accessory;
use App\Models\Equipment;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Button;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\EquipmentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EquipmentResource\RelationManagers;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;
    
    protected static ?string $navigationGroup = 'PMSi';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Equipment Form')->schema([
                    Group::make()->schema([
                        Section::make('')->schema([
                            Forms\Components\Select::make('customer_id')
                                ->required()
                                ->relationship('customer', 'name')
                                ->searchable(['name', 'id'])
                                ->preload()
                                ->prefixIcon('heroicon-o-user')
                                ->prefixIconColor('primary')
                                ->reactive()
                                ->afterStateHydrated(function (?string $state, callable $get, callable $set): void {
                                    $maxAr = Equipment::query()
                                        ->selectRaw('MAX(CAST(ar_id AS UNSIGNED)) as max')
                                        ->value('max') ?? 0;
                                    $toggle = $get('sameToggle');
                                    if ($toggle) {
                                        $customerId = Equipment::query()
                                            ->where('ar_id', $maxAr)
                                            ->value('customer_id');
                                        $set('customer_id', $customerId);
                                    }
                                }),
                            Forms\Components\TextInput::make('equipment_id')
                                ->required()  
                                ->label('Equipment Identification')  
                                ->maxLength(255),
                            Forms\Components\TextInput::make('manufacturer')
                                ->required()    
                                ->maxLength(255),
                            Forms\Components\TextInput::make('model')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('serial')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('description')
                                ->required()
                                ->maxLength(255),
                        ]),
                    ])
                    ->columnSpan(2),
                    Group::make()->schema([
                        Section::make('')->schema([
                            Forms\Components\Select::make('lab')
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
                            Forms\Components\Select::make('calType')
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
                            Forms\Components\Select::make('category')
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
                            Forms\Components\Select::make('inspection')
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
                            Forms\Components\DatePicker::make('inDate')
                                ->label('Date Received')
                                ->default(now())
                                ->required(),
                        ]),
                    ])
                    ->columnSpan(1),
                    Group::make()->schema([
                        Section::make('')->schema([
                            Forms\Components\Toggle::make('sameToggle')
                                ->label('Same')
                                ->onIcon('heroicon-m-bolt')
                                ->offIcon('heroicon-m-bolt')
                                ->onColor('success')
                                ->offColor('danger')
                                ->reactive()
                                ->default(true)
                                ->afterStateUpdated(function (bool $state, callable $get, callable $set): void {
                                    $maxAr = Equipment::query()
                                        ->selectRaw('MAX(CAST(ar_id AS UNSIGNED)) as max')
                                        ->value('max') ?? 0;
                                    // If toggle true, use max; otherwise, increment by one.
                                    $newValue = $state ? $maxAr : ((int)$maxAr + 1);
                                    $set('ar_id', (string)$newValue);

                                    if ($state) {
                                        $customerId = Equipment::query()
                                            ->where('ar_id', $maxAr)
                                            ->value('customer_id');
                                        $set('customer_id', $customerId);
                                    } else {
                                        // Set customer_id to blank when toggle is off
                                        $set('customer_id', null); 
                                    }
                                }),
                            // TextInput for ar_id: shows computed value and updates on hydration.
                            Forms\Components\TextInput::make('ar_id')
                                ->label('Acknowledgement Receipt No.')
                                ->readonly()
                                ->reactive()
                                ->prefix('401 -')
                                ->afterStateHydrated(function (?string $state, callable $get, callable $set): void {
                                    $maxAr = Equipment::query()
                                        ->selectRaw('MAX(CAST(ar_id AS UNSIGNED)) as max')
                                        ->value('max') ?? 0;
                                    $toggle = $get('sameToggle');
                                    $newValue = $toggle ? $maxAr : ((int)$maxAr + 1);
                                    $set('ar_id', (string)$newValue);

                                    if ($toggle) {
                                        $customerId = Equipment::query()
                                            ->where('ar_id', $maxAr)
                                            ->value('customer_id');
                                        $set('customer_id', $customerId);
                                    }
                                })
                                ->maxLength(255),
                            Forms\Components\Repeater::make('accessory')
                                ->relationship()
                                ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Transaction ID')
                    ->alignCenter()
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('equipment_id')
                    ->label('Equipment ID')
                    ->alignCenter()
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer Name')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('worksheet.name')
                    ->label('Worksheet')
                    ->default('No worksheet yet!')
                    ->alignCenter()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('manufacturer')
                    ->alignCenter()
                    ->label('Manufacturer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('inspection')
                    ->alignCenter()
                    ->label('Inspection Findings')
                    //This is just for capitalizing the words in the array
                    ->formatStateUsing(function ($state): string {return ucwords($state);})
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('lab')
                    ->label('Laboratory')
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('calType')
                    ->label('Calibration Type')
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category')
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('accessory.name')
                    ->listWithLineBreaks()
                    ->bulleted(),
                Tables\Columns\TextColumn::make('accessory.quantity')
                    ->label('Quantity')
                    ->listWithLineBreaks()
                    ->bulleted(),
                Tables\Columns\TextColumn::make('created_at')
                    ->alignCenter()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->alignCenter()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(), 
            ])
            ->actions([
                // ActionGroup::make([
                    
                    Tables\Actions\EditAction::make()
                        ->label('')
                        ->tooltip('Edit')
                        ->icon('heroicon-m-pencil-square')
                        ->color(Color::hex(Rgb::fromString('rgb('.Color::Pink[500].')')->toHex())),
                    Tables\Actions\Action::make('duplicate')
                        ->label('')
                        ->action(function (Equipment $record, $data) {
                            if ($data['with_accessories']) {
                                // Replicate the Equipment record
                                $newEquipment = $record->replicate();
                                $newEquipment->save();

                                // Replicate the related Accessory records
                                foreach ($record->accessory as $accessory) {
                                    $newAccessory = $accessory->replicate();
                                    $newAccessory->equipment_id = $newEquipment->id;
                                    $newAccessory->save();
                                }
                            } else {
                                // Replicate the Equipment record without accessories
                                $newEquipment = $record->replicate();
                                $newEquipment->save();
                            }
                            // Add notification
                            Notification::make()
                                ->title('Duplication Successful')
                                ->body('The equipment has been successfully duplicated.')
                                ->success()
                                ->send();
                        })
                        ->form([
                            Forms\Components\Toggle::make('with_accessories')
                                ->label('Duplicate with Accessories?')
                                ->default(true)
                                ->onIcon('heroicon-m-bolt')
                                ->offIcon('heroicon-m-bolt-slash')
                                ->onColor('success')
                                ->offColor('danger')
                        ])
                        ->icon('heroicon-m-document-duplicate')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-document-duplicate')
                        ->modalHeading('Duplicate Equipment')
                        ->modalSubheading('Do you want to duplicate this equipment with accessories?')
                        ->modalButton('Duplicate')
                        ->tooltip('Duplicate')
                        ->color('primary'),
                    Tables\Actions\DeleteAction::make()
                        ->label('')
                        ->tooltip('Delete')
                        ->modalIcon('heroicon-o-trash')
                        ->modalHeading(fn (Equipment $record) => 'Remove ' . $record->manufacturer)
                        ->modalDescription(fn (Equipment $record) => 'Are you sure you want to remove ' . $record->manufacturer . ' equipment?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-trash')
                                ->title('Equipment Removed')
                                ->body('The equipment has been removed successfully.'),
                        ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->modalIcon('heroicon-o-trash')
                        ->modalHeading(fn (Equipment $record) => 'Remove ' . $record->manufacturer . ' permanently?')
                        ->modalDescription(fn (Equipment $record) => 'Are you sure you want to remove ' . $record->manufacturer . ' equipment permanently?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-trash')
                                ->title('Equipment Removed Permanently')
                                ->body('The equipment has been permanently removed.'),
                        ),
                    Tables\Actions\RestoreAction::make()
                        ->color('primary')
                        ->modalIcon('heroicon-o-cube')
                        ->modalHeading(fn (Equipment $record) => 'Bring ' . $record->manufacturer . ' back')
                        ->modalDescription(fn (Equipment $record) => 'Are you sure you want to bring back ' . $record->manufacturer . ' in our equipments?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-cube')
                                ->title('Equipment Restored')
                                ->body('The equipment has been restored succesfully.'),
                        ),
                // ])
                // ->icon('heroicon-o-cog-6-tooth')
                // ->tooltip('Options') 
                // ->color('danger')
                ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 20, 40])
            ->extremePaginationLinks();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEquipment::route('/'),
            'create' => Pages\CreateEquipment::route('/create'),
            'edit' => Pages\EditEquipment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
