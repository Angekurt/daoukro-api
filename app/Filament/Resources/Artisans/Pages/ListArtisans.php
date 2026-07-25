<?php

namespace App\Filament\Resources\Artisans\Pages;

use App\Filament\Resources\Artisans\ArtisanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtisans extends ListRecords
{
    protected static string $resource = ArtisanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
