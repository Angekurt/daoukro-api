<?php

namespace App\Filament\Resources\Prestataires\Pages;

use App\Filament\Resources\Prestataires\PrestataireResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrestataire extends EditRecord
{
    protected static string $resource = PrestataireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
