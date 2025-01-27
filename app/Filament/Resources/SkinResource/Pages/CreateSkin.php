<?php

namespace App\Filament\Resources\SkinResource\Pages;

use App\Filament\Resources\SkinResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSkin extends CreateRecord
{
    protected static string $resource = SkinResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
