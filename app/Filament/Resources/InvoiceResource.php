<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\InvoiceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InvoiceResource\RelationManagers;

class InvoiceResource extends Resource
{
    protected static ?string $navigationGroup = 'PMSi';
    
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'bi-envelope-paper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // I use this only for Edit
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_date')
                    ->label('Invoice Date')
                    ->date(),
                
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice Number')
                    ->color('info')
                    ->formatStateUsing(fn ($state) => '83-00-' . $state),
                
                Tables\Columns\TextColumn::make('customer_id')
                    ->label('Customer')
                    ->color('primary')
                    ->formatStateUsing(function ($state) {
                        $customer = Customer::where('customer_id', $state)->first();
                        return $customer ? $customer->name : 'Unknown';
                    }),
                
                Tables\Columns\TextColumn::make('contactPerson')
                    ->label('Attention To'),
                
                Tables\Columns\TextColumn::make('carbonCopy')
                    ->label('CC'),

                Tables\Columns\TextColumn::make('poNoCalibration')
                    ->label('Purchase Order')
                    ->searchable(),

                Tables\Columns\TextColumn::make('yourRef')
                    ->label('Your Ref')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('pmsiRefNo')
                    ->label('PMSi Ref')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('freeOnBoard')
                    ->label('FOB')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('businessSystem')
                    ->label('Business System')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tin')
                    ->label('TIN #')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('service')
                    ->formatStateUsing(function ($state) {
                        $services = [
                            'calibration' => 'Calibration',
                            'repair' => 'Repair',
                            'realignment' => 'Re-alignment',
                            'cal_repair' => 'Cal / Repair',
                            'cal_realign' => 'Cal / Re-align',
                            'repair_realign' => 'Repair / Realign',
                            'cal_repair_realign' => 'Cal / Repair / Realign',
                        ];
                
                        return $services[$state] ?? 'N/A';
                    }),

                Tables\Columns\TextColumn::make('payment')
                ->formatStateUsing(fn ($record) => ($record->payment === 'cod' ? 'Cash On Delivery' : $record->payment)),

                Tables\Columns\TextColumn::make('subTotal')
                    ->money(fn ($record) => ($record->currency === 'PHP' ? 'PHP' : 'USD')),

                Tables\Columns\TextColumn::make('vatToggle')
                    ->label('Vat Inclusive')
                    ->formatStateUsing(fn ($record) => ($record->vatToggle ? 'True' : 'False'))
                    ->badge()
                    ->color(fn ($state) => ($state ? 'primary' : 'gray')),

                Tables\Columns\TextColumn::make('currency'),

                Tables\Columns\TextColumn::make('total')
                    ->money(fn ($record) => ($record->currency === 'PHP' ? 'PHP' : 'USD')),

                Tables\Columns\TextColumn::make('amountInWords')
                    ->label('Amount In Words')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('comments')
                    ->wrap()
                    ->default('No Comment'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->tooltip('Options')
                    ->color('danger'),
                Tables\Actions\Action::make('printInvoice')
                    ->tooltip('Print Invoice')
                    ->icon('bi-printer-fill')
                    ->label('Print Invoice')
                    ->requiresConfirmation()
                    ->modalIcon('bi-printer')
                    ->modalHeading('Ready to Print Invoice')
                    ->modalDescription('Make sure all invoice details are accurate. When you\'re ready, click “Print” to generate a printable copy.')
                    ->modalSubmitActionLabel('Print')
                    ->url(fn ($record) => route('invoice-manager', ['invoice_id' => $record->id]))
                    ->openUrlInNewTab(),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInvoices::route('/'),
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
