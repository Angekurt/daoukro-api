<?php

namespace App\Filament\Resources\Hebergements\Pages;

use App\Filament\Resources\Hebergements\HebergementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHebergements extends ListRecords
{
    protected static string $resource = HebergementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
