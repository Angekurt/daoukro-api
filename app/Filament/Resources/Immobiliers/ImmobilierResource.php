<?php

namespace App\Filament\Resources\Immobiliers;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\Immobiliers\Pages\CreateImmobilier;
use App\Filament\Resources\Immobiliers\Pages\EditImmobilier;
use App\Filament\Resources\Immobiliers\Pages\ListImmobiliers;
use App\Filament\Resources\Immobiliers\Schemas\ImmobilierForm;
use App\Filament\Resources\Immobiliers\Tables\ImmobiliersTable;
use App\Models\Immobilier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ImmobilierResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = Immobilier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static ?string $recordTitleAttribute = 'titre';

    public static function form(Schema $schema): Schema
    {
        return ImmobilierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ImmobiliersTable::configure($table);
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
            'index' => ListImmobiliers::route('/'),
            'create' => CreateImmobilier::route('/create'),
            'edit' => EditImmobilier::route('/{record}/edit'),
        ];
    }
}
