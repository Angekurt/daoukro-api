<?php

namespace App\Filament\Resources\Immobiliers\Pages;

use App\Filament\Resources\Immobiliers\ImmobilierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListImmobiliers extends ListRecords
{
    protected static string $resource = ImmobilierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
