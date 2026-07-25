<?php

namespace App\Filament\Resources\Pharmacies\Pages;

use App\Filament\Resources\Pharmacies\PharmacieResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPharmacie extends EditRecord
{
    protected static string $resource = PharmacieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
