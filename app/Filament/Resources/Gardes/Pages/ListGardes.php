<?php

namespace App\Filament\Resources\Gardes\Pages;

use App\Filament\Resources\Gardes\GardeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGardes extends ListRecords
{
    protected static string $resource = GardeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
