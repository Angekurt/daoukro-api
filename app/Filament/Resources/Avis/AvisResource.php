<?php

namespace App\Filament\Resources\Avis;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\Avis\Pages\EditAvis;
use App\Filament\Resources\Avis\Pages\ListAvis;
use App\Filament\Resources\Avis\Schemas\AvisForm;
use App\Filament\Resources\Avis\Tables\AvisTable;
use App\Models\Avis as AvisModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AvisResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = AvisModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?string $recordTitleAttribute = 'nom';

    protected static ?string $navigationLabel = 'Avis (modération)';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::pending()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return AvisForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AvisTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAvis::route('/'),
            'edit' => EditAvis::route('/{record}/edit'),
        ];
    }
}
