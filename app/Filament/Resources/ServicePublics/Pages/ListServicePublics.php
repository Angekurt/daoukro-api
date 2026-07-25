<?php

namespace App\Filament\Resources\ServicePublics\Pages;

use App\Filament\Resources\ServicePublics\ServicePublicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServicePublics extends ListRecords
{
    protected static string $resource = ServicePublicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
