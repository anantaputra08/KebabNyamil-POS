<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    public static ?int $navigationSort = 10;
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationIcon = 'fas-user-edit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->nullable()
                    ->maxLength(255)
                    ->dehydrateStateUsing(function ($state, $record) {
                        // If no new password is provided, use the existing password
                        return $state ? bcrypt($state) : $record->password;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->formatStateUsing(function ($state) {
                        $updatedAt = Carbon::parse($state);
                        $now = Carbon::now();
                        $diff = $updatedAt->diff($now);

                        $days = $diff->d;
                        $hours = $diff->h;
                        $minutes = $diff->i;

                        $timeString = '';

                        if ($days > 0) {
                            $timeString .= $days . ' day' . ($days > 1 ? 's' : '') . ' ';
                        }
                        if ($hours > 0) {
                            $timeString .= $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ';
                        }
                        if ($minutes > 0) {
                            $timeString .= $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ';
                        }

                        return trim($timeString) . ' ago';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
}
