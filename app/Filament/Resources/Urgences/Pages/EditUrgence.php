<?php

namespace App\Filament\Resources\Urgences\Pages;

use App\Filament\Resources\Urgences\UrgenceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUrgence extends EditRecord
{
    protected static string $resource = UrgenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
