<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpicyLevelResource\Pages;
use App\Filament\Resources\SpicyLevelResource\RelationManagers;
use App\Models\SpicyLevel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SpicyLevelResource extends Resource
{
    protected static ?string $model = SpicyLevel::class;
    public static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Product Management';
    protected static ?string $navigationIcon = 'bxs-hot';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListSpicyLevels::route('/'),
            'create' => Pages\CreateSpicyLevel::route('/create'),
            'edit' => Pages\EditSpicyLevel::route('/{record}/edit'),
        ];
    }
}
