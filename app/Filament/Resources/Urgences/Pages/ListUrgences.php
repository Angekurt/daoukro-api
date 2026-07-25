<?php

namespace App\Filament\Resources\Urgences\Pages;

use App\Filament\Resources\Urgences\UrgenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUrgences extends ListRecords
{
    protected static string $resource = UrgenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
