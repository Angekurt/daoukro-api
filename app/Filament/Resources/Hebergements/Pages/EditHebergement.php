<?php

namespace App\Filament\Resources\Hebergements\Pages;

use App\Filament\Resources\Hebergements\HebergementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHebergement extends EditRecord
{
    protected static string $resource = HebergementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
