<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesReportResource\Pages;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;

class SalesReportResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Sales Report';
    protected static ?string $navigationGroup = 'Sales Management';
    public static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Transaction Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kasir')
                    ->searchable(),

                Tables\Columns\TextColumn::make('items')
                    ->label('Products')
                    ->state(function (Transaction $record) {
                        return $record->items->map(function ($item) {
                            return "{$item->menu->name} (Kulit: {$item->skin->name}, Daging: {$item->meat->name})";
                        })->implode("\n");
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('total_items')
                    ->label('Total Items')
                    ->state(function (Transaction $record) {
                        return $record->items->count();
                    }),

                Tables\Columns\TextColumn::make('gross_amount')
                    ->label('Total Sales')
                    ->money('IDR')
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(function ($state) {
                                return "IDR " . number_format($state, 2, ',', '.');
                            })
                    ),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_type')
                    ->label('Payment Method')
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from_date')->label('From'),
                        DatePicker::make('to_date')->label('To'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    })
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalesReports::route('/'),
        ];
    }
}