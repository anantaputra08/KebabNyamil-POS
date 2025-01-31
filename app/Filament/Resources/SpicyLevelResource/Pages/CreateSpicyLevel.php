<?php

namespace App\Filament\Resources\SpicyLevelResource\Pages;

use App\Filament\Resources\SpicyLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSpicyLevel extends CreateRecord
{
    protected static string $resource = SpicyLevelResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
