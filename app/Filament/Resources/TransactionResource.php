<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    public static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Sales Management';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_id')
                    ->label('Order ID')
                    ->default(fn() => rand(100000, 999999)) // Generate otomatis
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                Forms\Components\TextInput::make('user_id')
                    ->label('User ID')
                    ->default(fn() => Auth::id()) // Ambil user yang login
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->default('pending')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->dehydrated()
                    ->required(),

                Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        Forms\Components\Select::make('menu_id')
                            ->label('Menu')
                            ->options(fn() => \App\Models\Menu::pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                self::updateItemPrice($set, $get)
                            ),

                        Forms\Components\Select::make('skin_id')
                            ->label('Kulit')
                            ->options(fn(callable $get) => \App\Models\Skin::where('menu_id', $get('menu_id'))->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                self::updateItemPrice($set, $get)
                            ),

                        Forms\Components\Select::make('meat_id')
                            ->label('Daging')
                            ->options(fn() => \App\Models\Meat::pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                self::updateItemPrice($set, $get)
                            ),

                        Forms\Components\Select::make('spicy_level_id')
                            ->label('Level Pedas')
                            ->options(fn() => \App\Models\SpicyLevel::pluck('name', 'id'))
                            ->required(),

                        Forms\Components\CheckboxList::make('extras')
                            ->label('Extras')
                            ->options(fn() => \App\Models\Extra::pluck('name', 'id'))
                            ->columns(2)
                            ->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                self::updateItemPrice($set, $get)
                            ),

                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp.')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                self::updateTotalPrice($set, $get) // Tambahkan di sini
                            ),
                    ])
                    ->afterStateUpdated(
                        fn($state, callable $set, callable $get) =>
                        self::updateTotalPrice($set, $get)
                    ),

                Forms\Components\Select::make('payment_type')
                    ->label('Payment Type')
                    ->options([
                        'cash' => 'Cash',
                        'credit_card' => 'Credit Card',
                        'bank_transfer' => 'Bank Transfer',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('gross_amount')
                    ->label('Gross Amount')
                    ->numeric()
                    ->prefix('Rp.')
                    ->disabled()
                    ->dehydrated()
                    ->reactive(),
            ]);
    }
    public static function updateItemPrice(callable $set, callable $get)
    {
        $menuPrice = \App\Models\Menu::find($get('menu_id'))?->price ?? 0;
        $skinPrice = \App\Models\Skin::find($get('skin_id'))?->price ?? 0;
        $meatPrice = \App\Models\Meat::find($get('meat_id'))?->price ?? 0;
        $extraPrices = \App\Models\Extra::whereIn('id', $get('extras') ?? [])->sum('price');

        $totalItemPrice = $menuPrice + $skinPrice + $meatPrice + $extraPrices;
        $set('price', $totalItemPrice);

        // Panggil updateTotalPrice setelah harga item diubah
        self::updateTotalPrice($set, $get);
    }
    public static function updateTotalPrice(callable $set, callable $get)
    {
        // Pastikan setiap item memiliki harga yang terbaru sebelum menjumlahkan
        $items = collect($get('items') ?? [])->map(function ($item) {
            $menuPrice = \App\Models\Menu::find($item['menu_id'])?->price ?? 0;
            $skinPrice = \App\Models\Skin::find($item['skin_id'])?->price ?? 0;
            $meatPrice = \App\Models\Meat::find($item['meat_id'])?->price ?? 0;
            $extraPrices = \App\Models\Extra::whereIn('id', $item['extras'] ?? [])->sum('price');

            return $menuPrice + $skinPrice + $meatPrice + $extraPrices;
        });

        // Set total harga dengan hasil penjumlahan terbaru
        $set('gross_amount', $items->sum());
    }
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id')
                //     ->sortable(),
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('gross_amount')
                    ->label('Gross Amount')
                    ->sortable()
                    ->money('Rp.'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($state),
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'pending' => 'warning', // Warna kuning untuk pending
                            'completed' => 'success', // Warna hijau untuk completed
                            'cancelled' => 'danger', // Warna merah untuk cancelled
                            default => 'gray', // Warna default jika tidak ada yang cocok
                        };
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_type')
                    ->label('Payment Type')
                    ->sortable()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'cash' => 'Cash',
                        'credit_card' => 'Credit Card',
                        'bank_transfer' => 'Bank Transfer',
                        default => ucfirst($state),
                    })
                    ->color(fn($state) => match ($state) {
                        'cash' => 'success', // Hijau untuk Cash
                        'credit_card' => 'primary', // Biru untuk Credit Card
                        'bank_transfer' => 'info', // Kuning untuk Bank Transfer
                        default => 'gray',
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('markAsCompleted')
                    ->label('Mark as Completed')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending') // Tampilkan hanya jika status pending
                    ->action(fn($record) => $record->update(['status' => 'completed'])),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->recordAction('view');
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
