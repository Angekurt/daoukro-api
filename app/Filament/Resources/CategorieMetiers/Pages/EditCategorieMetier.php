<?php

namespace App\Filament\Resources\CategorieMetiers\Pages;

use App\Filament\Resources\CategorieMetiers\CategorieMetierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategorieMetier extends EditRecord
{
    protected static string $resource = CategorieMetierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
