<?php

namespace App\Filament\Resources\Signalements;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\Signalements\Pages\EditSignalement;
use App\Filament\Resources\Signalements\Pages\ListSignalements;
use App\Models\Signalement;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SignalementResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = Signalement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $recordTitleAttribute = 'description';

    protected static ?string $navigationLabel = 'Signalements citoyens';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::enAttente()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('categorie')->disabled(),
            Textarea::make('description')->disabled()->columnSpanFull(),
            TextInput::make('adresse')->disabled(),
            TextInput::make('auteur')->label('Auteur')->disabled(),
            TextInput::make('telephone')->disabled(),
            Select::make('statut')
                ->options([
                    'en_attente' => 'En attente',
                    'en_cours' => 'En cours de traitement',
                    'resolu' => 'Résolu',
                ])
                ->required(),
            Textarea::make('note_admin')
                ->label('Note interne (suivi, actions menées...)')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Resources\Signalements\Tables\SignalementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSignalements::route('/'),
            'edit' => EditSignalement::route('/{record}/edit'),
        ];
    }
}
