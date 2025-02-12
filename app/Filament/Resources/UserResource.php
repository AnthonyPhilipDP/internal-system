<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Grid::make()->schema([
                        FileUpload::make('avatar_url')
                            ->avatar()
                            ->directory('avatars'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder('Juan Dela Cruz')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('username')
                            ->nullable()
                            ->placeholder('Anything you like ^_^')
                            ->maxLength(255),
                        Forms\Components\Select::make('level')
                            ->label('User Level')
                            ->required()
                            ->options(User::LEVEL)
                            ->default(User::EMPLOYEE),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->default(now()),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->placeholder('Enter your desired password')
                            ->required()
                            ->revealable()
                            ->maxLength(255),
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->alignCenter()
                    ->searchable(),
                ImageColumn::make('avatar_url'),
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
                    Tables\Actions\ViewAction::make(),
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
