<?php

namespace App\Filament\Resources\Pharmacies\Pages;

use App\Filament\Resources\Pharmacies\PharmacieResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPharmacies extends ListRecords
{
    protected static string $resource = PharmacieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
