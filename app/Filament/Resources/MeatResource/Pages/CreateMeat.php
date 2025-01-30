<?php

namespace App\Filament\Resources\MeatResource\Pages;

use App\Filament\Resources\MeatResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMeat extends CreateRecord
{
    protected static string $resource = MeatResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
