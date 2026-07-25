<?php

namespace App\Filament\Resources\CategorieServices;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\CategorieServices\Pages\CreateCategorieService;
use App\Filament\Resources\CategorieServices\Pages\EditCategorieService;
use App\Filament\Resources\CategorieServices\Pages\ListCategorieServices;
use App\Models\CategorieService;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategorieServiceResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = CategorieService::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'nom';

    protected static ?string $navigationLabel = 'Catégories (services)';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nom')->required()->maxLength(100),
            TextInput::make('icone')->helperText('Nom d\'icône (ex. local_hospital)'),
            TextInput::make('couleur')->helperText('Ex. #145217'),
            TextInput::make('ordre')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')->searchable()->weight('bold'),
                TextColumn::make('ordre')->sortable(),
            ])
            ->defaultSort('ordre')
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
            'index' => ListCategorieServices::route('/'),
            'create' => CreateCategorieService::route('/create'),
            'edit' => EditCategorieService::route('/{record}/edit'),
        ];
    }
}
