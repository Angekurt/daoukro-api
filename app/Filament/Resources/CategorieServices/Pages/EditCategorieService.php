<?php

namespace App\Filament\Resources\CategorieServices\Pages;

use App\Filament\Resources\CategorieServices\CategorieServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategorieService extends EditRecord
{
    protected static string $resource = CategorieServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
