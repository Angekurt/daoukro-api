<?php

namespace App\Filament\Resources\Prestataires;

use App\Filament\Concerns\RestreintSuppressionAdmin;
use App\Filament\Resources\Prestataires\Pages\EditPrestataire;
use App\Filament\Resources\Prestataires\Pages\ListPrestataires;
use App\Models\Prestataire;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrestataireResource extends Resource
{
    use RestreintSuppressionAdmin;

    protected static ?string $model = Prestataire::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $recordTitleAttribute = 'nom_complet';

    protected static ?string $navigationLabel = 'Candidatures artisans';

    // Badge avec le nombre de candidatures en attente, visible dans le menu.
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::pending()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nom_complet')->required()->disabled(),
            TextInput::make('telephone')->disabled(),
            Textarea::make('description')->disabled()->columnSpanFull(),
            TextInput::make('zone_intervention')->disabled(),
            Select::make('statut')
                ->options([
                    'pending' => 'En attente',
                    'approved' => 'Approuvé',
                    'rejected' => 'Rejeté',
                ])
                ->required(),
            Textarea::make('note_admin')
                ->label('Note interne (motif de rejet, remarque...)')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Resources\Prestataires\Tables\PrestatairesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrestataires::route('/'),
            'edit' => EditPrestataire::route('/{record}/edit'),
        ];
    }
}
