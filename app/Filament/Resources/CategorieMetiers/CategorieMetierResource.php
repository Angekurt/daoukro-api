<?php

namespace App\Filament\Resources\CategorieMetiers;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\CategorieMetiers\Pages\CreateCategorieMetier;
use App\Filament\Resources\CategorieMetiers\Pages\EditCategorieMetier;
use App\Filament\Resources\CategorieMetiers\Pages\ListCategorieMetiers;
use App\Models\CategorieMetier;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategorieMetierResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = CategorieMetier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $recordTitleAttribute = 'nom';

    protected static ?string $navigationLabel = 'Catégories (métiers)';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nom')->required()->maxLength(100),
            TextInput::make('description'),
            TextInput::make('icone')->helperText('Nom d\'icône (ex. handyman)'),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')->searchable()->weight('bold'),
                IconColumn::make('is_active')->label('Actif')->boolean(),
            ])
            ->recordActions([\Filament\Actions\EditAction::make()])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategorieMetiers::route('/'),
            'create' => CreateCategorieMetier::route('/create'),
            'edit' => EditCategorieMetier::route('/{record}/edit'),
        ];
    }
}
