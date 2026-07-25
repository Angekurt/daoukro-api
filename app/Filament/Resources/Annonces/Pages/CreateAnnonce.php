<?php

namespace App\Filament\Resources\Annonces\Pages;

use App\Filament\Resources\Annonces\AnnonceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnonce extends CreateRecord
{
    protected static string $resource = AnnonceResource::class;
}
