<?php

namespace App\Filament\Resources\CategorieServices\Pages;

use App\Filament\Resources\CategorieServices\CategorieServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategorieServices extends ListRecords
{
    protected static string $resource = CategorieServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
