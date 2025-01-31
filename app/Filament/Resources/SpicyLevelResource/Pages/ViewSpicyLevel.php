<?php

namespace App\Filament\Resources\SpicyLevelResource\Pages;

use App\Filament\Resources\SpicyLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSpicyLevel extends ViewRecord
{
    protected static string $resource = SpicyLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
