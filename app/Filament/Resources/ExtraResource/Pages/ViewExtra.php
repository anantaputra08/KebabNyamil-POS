<?php

namespace App\Filament\Resources\ExtraResource\Pages;

use App\Filament\Resources\ExtraResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExtra extends ViewRecord
{
    protected static string $resource = ExtraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
