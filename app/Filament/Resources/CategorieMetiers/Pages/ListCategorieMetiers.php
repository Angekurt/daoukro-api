<?php

namespace App\Filament\Resources\CategorieMetiers\Pages;

use App\Filament\Resources\CategorieMetiers\CategorieMetierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategorieMetiers extends ListRecords
{
    protected static string $resource = CategorieMetierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
