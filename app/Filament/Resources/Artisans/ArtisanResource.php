<?php

namespace App\Filament\Resources\Artisans;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\Artisans\Pages\CreateArtisan;
use App\Filament\Resources\Artisans\Pages\EditArtisan;
use App\Filament\Resources\Artisans\Pages\ListArtisans;
use App\Filament\Resources\Artisans\Schemas\ArtisanForm;
use App\Filament\Resources\Artisans\Tables\ArtisansTable;
use App\Models\Artisan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ArtisanResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = Artisan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $recordTitleAttribute = 'nom';

    public static function form(Schema $schema): Schema
    {
        return ArtisanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArtisansTable::configure($table);
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
            'index' => ListArtisans::route('/'),
            'create' => CreateArtisan::route('/create'),
            'edit' => EditArtisan::route('/{record}/edit'),
        ];
    }
}
