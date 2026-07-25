<?php

namespace App\Filament\Resources\Immobiliers\Pages;

use App\Filament\Resources\Immobiliers\ImmobilierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditImmobilier extends EditRecord
{
    protected static string $resource = ImmobilierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
