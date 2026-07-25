<?php

namespace App\Filament\Resources\Gardes\Pages;

use App\Filament\Resources\Gardes\GardeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGarde extends EditRecord
{
    protected static string $resource = GardeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
