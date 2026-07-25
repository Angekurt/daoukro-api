<?php

namespace App\Filament\Resources\Artisans\Pages;

use App\Filament\Resources\Artisans\ArtisanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArtisan extends EditRecord
{
    protected static string $resource = ArtisanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
