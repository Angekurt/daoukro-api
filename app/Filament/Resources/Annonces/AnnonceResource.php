<?php

namespace App\Filament\Resources\Annonces;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\Annonces\Pages\CreateAnnonce;
use App\Filament\Resources\Annonces\Pages\EditAnnonce;
use App\Filament\Resources\Annonces\Pages\ListAnnonces;
use App\Filament\Resources\Annonces\Schemas\AnnonceForm;
use App\Filament\Resources\Annonces\Tables\AnnoncesTable;
use App\Models\Annonce;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnnonceResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = Annonce::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?string $recordTitleAttribute = 'titre';

    public static function form(Schema $schema): Schema
    {
        return AnnonceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnoncesTable::configure($table);
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
            'index' => ListAnnonces::route('/'),
            'create' => CreateAnnonce::route('/create'),
            'edit' => EditAnnonce::route('/{record}/edit'),
        ];
    }
}
