<?php

namespace App\Filament\Resources\MeatResource\Pages;

use App\Filament\Resources\MeatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMeats extends ListRecords
{
    protected static string $resource = MeatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
