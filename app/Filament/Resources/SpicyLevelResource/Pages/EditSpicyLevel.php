<?php

namespace App\Filament\Resources\SpicyLevelResource\Pages;

use App\Filament\Resources\SpicyLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpicyLevel extends EditRecord
{
    protected static string $resource = SpicyLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
