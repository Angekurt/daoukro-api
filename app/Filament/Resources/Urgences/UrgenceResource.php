<?php

namespace App\Filament\Resources\Urgences;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\Urgences\Pages\CreateUrgence;
use App\Filament\Resources\Urgences\Pages\EditUrgence;
use App\Filament\Resources\Urgences\Pages\ListUrgences;
use App\Filament\Resources\Urgences\Schemas\UrgenceForm;
use App\Filament\Resources\Urgences\Tables\UrgencesTable;
use App\Models\Urgence;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UrgenceResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = Urgence::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static ?string $recordTitleAttribute = 'nom';

    public static function form(Schema $schema): Schema
    {
        return UrgenceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UrgencesTable::configure($table);
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
            'index' => ListUrgences::route('/'),
            'create' => CreateUrgence::route('/create'),
            'edit' => EditUrgence::route('/{record}/edit'),
        ];
    }
}
