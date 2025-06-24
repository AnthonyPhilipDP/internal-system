<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $label = 'Employees';

    protected static ?string $navigationLabel = 'Employees';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Credentials')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->placeholder('Juan Dela Cruz')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->placeholder('Enter your desired password')
                                ->required()
                                ->revealable()
                                ->maxLength(255),
                        ])
                        ->icon('heroicon-o-newspaper')
                        ->completedIcon('heroicon-m-newspaper'),
                    Wizard\Step::make('Administrative Information')
                        ->schema([
                            Forms\Components\Select::make('level')
                            ->label('User Level')
                            ->required()
                            ->native(false)
                            ->options(User::LEVEL)
                            ->default(User::EMPLOYEE),
                            Forms\Components\Toggle::make('has_invoice_access')
                            ->label('Invoice Access')
                            ->helperText('Turn on to allow this user to access the invoice management system')
                            ->default(false),
                        ])
                        ->icon('heroicon-o-finger-print')
                        ->completedIcon('heroicon-m-finger-print'),
                    Wizard\Step::make('Profile Picture')
                        ->schema([
                            FileUpload::make('avatarUrl')
                                ->label('Avatar (optional)')
                                ->avatar()
                                ->directory('avatars')
                                ->alignCenter()
                                ->imageEditor()
                                ->circleCropper()
                        ])
                        ->icon('heroicon-o-user-circle')
                        ->completedIcon('heroicon-m-user-circle'),
                    ])->skippable(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->alignCenter()
                    ->searchable(),
                ImageColumn::make('avatarUrl')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(asset('images/default avatar.png'))
                    ->extraImgAttributes(['loading' => 'lazy']),
                Tables\Columns\TextColumn::make('username')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('level')
                    ->alignCenter()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            1 => 'Administrator',
                            2 => 'Employee',
                            3 => 'Guest',
                        };
                    })
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('has_invoice_access')
                    ->label('Invoice Access'),
                // Tables\Columns\TextColumn::make('email_verified_at')
                //     ->alignCenter()
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(), 
            ])
            ->actions([
                ActionGroup::make([
                    // Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->modalIcon('heroicon-o-user-minus')
                        ->modalHeading(fn (User $record) => 'Remove ' . $record->name)
                        ->modalDescription(fn (User $record) => 'Are you sure you want to remove ' . $record->name . ' as an Employee?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-user-minus')
                                ->title('User Removed')
                                ->body('The user has been removed successfully.'),
                         ),
                    Tables\Actions\ForceDeleteAction::make()
                        ->modalIcon('heroicon-o-user-minus')
                        ->modalHeading(fn (User $record) => 'Remove ' . $record->name . ' permanently')
                        ->modalDescription(fn (User $record) => 'Are you sure you want to remove ' . $record->name . ' as an Employee permanently?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-user-minus')
                                ->title('User Removed Permanently')
                                ->body('The user has been permanently removed.'),
                         ),
                    Tables\Actions\RestoreAction::make()
                        ->color('primary')
                        ->modalIcon('heroicon-o-user-plus')
                        ->modalHeading(fn (User $record) => 'Bring ' . $record->name . ' back')
                        ->modalDescription(fn (User $record) => 'Are you sure you want to bring back ' . $record->name . ' as an Employee?')
                        ->modalSubmitActionLabel('Yes')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-user-plus')
                                ->title('User Restored')
                                ->body('The user has been restored succesfully.'),
                        )
                ])
                ->icon('heroicon-o-cog-6-tooth')
                ->tooltip('Options')
                ->color('danger')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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
