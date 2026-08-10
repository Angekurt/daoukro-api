<?php

namespace App\Filament\Resources\Citoyens\Pages;

use App\Filament\Resources\Citoyens\CitoyenResource;
use Filament\Resources\Pages\EditRecord;

class EditCitoyen extends EditRecord
{
    protected static string $resource = CitoyenResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
