<?php

namespace App\Filament\Resources\Hebergements;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\Hebergements\Pages\CreateHebergement;
use App\Filament\Resources\Hebergements\Pages\EditHebergement;
use App\Filament\Resources\Hebergements\Pages\ListHebergements;
use App\Filament\Resources\Hebergements\Schemas\HebergementForm;
use App\Filament\Resources\Hebergements\Tables\HebergementsTable;
use App\Models\Hebergement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HebergementResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = Hebergement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $recordTitleAttribute = 'nom';

    public static function form(Schema $schema): Schema
    {
        return HebergementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HebergementsTable::configure($table);
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
            'index' => ListHebergements::route('/'),
            'create' => CreateHebergement::route('/create'),
            'edit' => EditHebergement::route('/{record}/edit'),
        ];
    }
}
