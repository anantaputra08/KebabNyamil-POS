<?php

namespace App\Filament\Resources\MeatResource\Pages;

use App\Filament\Resources\MeatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMeat extends EditRecord
{
    protected static string $resource = MeatResource::class;

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
