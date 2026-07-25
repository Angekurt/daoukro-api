<?php

namespace App\Filament\Resources\ServicePublics;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\ServicePublics\Pages\CreateServicePublic;
use App\Filament\Resources\ServicePublics\Pages\EditServicePublic;
use App\Filament\Resources\ServicePublics\Pages\ListServicePublics;
use App\Filament\Resources\ServicePublics\Schemas\ServicePublicForm;
use App\Filament\Resources\ServicePublics\Tables\ServicePublicsTable;
use App\Models\ServicePublic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServicePublicResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = ServicePublic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $recordTitleAttribute = 'nom';

    public static function form(Schema $schema): Schema
    {
        return ServicePublicForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServicePublicsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServicePublics::route('/'),
            'create' => CreateServicePublic::route('/create'),
            'edit' => EditServicePublic::route('/{record}/edit'),
        ];
    }
}
