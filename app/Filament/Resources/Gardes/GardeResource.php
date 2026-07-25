<?php

namespace App\Filament\Resources\Gardes;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\Gardes\Pages\CreateGarde;
use App\Filament\Resources\Gardes\Pages\EditGarde;
use App\Filament\Resources\Gardes\Pages\ListGardes;
use App\Filament\Resources\Gardes\Schemas\GardeForm;
use App\Filament\Resources\Gardes\Tables\GardesTable;
use App\Models\Garde;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GardeResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = Garde::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $recordTitleAttribute = 'note';

    public static function form(Schema $schema): Schema
    {
        return GardeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GardesTable::configure($table);
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
            'index' => ListGardes::route('/'),
            'create' => CreateGarde::route('/create'),
            'edit' => EditGarde::route('/{record}/edit'),
        ];
    }
}
