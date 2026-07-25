<?php

namespace App\Filament\Resources\Pharmacies;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\Pharmacies\Pages\CreatePharmacie;
use App\Filament\Resources\Pharmacies\Pages\EditPharmacie;
use App\Filament\Resources\Pharmacies\Pages\ListPharmacies;
use App\Filament\Resources\Pharmacies\Schemas\PharmacieForm;
use App\Filament\Resources\Pharmacies\Tables\PharmaciesTable;
use App\Models\Pharmacie;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PharmacieResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = Pharmacie::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $recordTitleAttribute = 'nom';

    public static function form(Schema $schema): Schema
    {
        return PharmacieForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PharmaciesTable::configure($table);
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
            'index' => ListPharmacies::route('/'),
            'create' => CreatePharmacie::route('/create'),
            'edit' => EditPharmacie::route('/{record}/edit'),
        ];
    }
}
