<?php

namespace App\Filament\Resources\Citoyens\Pages;

use App\Filament\Resources\Citoyens\CitoyenResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCitoyens extends ListRecords
{
    protected static string $resource = CitoyenResource::class;
}
