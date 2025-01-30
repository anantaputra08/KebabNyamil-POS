<?php

namespace App\Filament\Resources\MeatResource\Pages;

use App\Filament\Resources\MeatResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMeat extends ViewRecord
{
    protected static string $resource = MeatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
