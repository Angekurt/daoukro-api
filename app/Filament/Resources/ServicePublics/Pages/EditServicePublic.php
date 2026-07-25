<?php

namespace App\Filament\Resources\ServicePublics\Pages;

use App\Filament\Resources\ServicePublics\ServicePublicResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServicePublic extends EditRecord
{
    protected static string $resource = ServicePublicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
