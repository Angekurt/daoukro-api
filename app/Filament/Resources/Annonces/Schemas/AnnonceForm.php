<?php

namespace App\Filament\Resources\Annonces\Schemas;

use App\Models\Ville;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnonceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ville_id')
                    ->label('Ville')
                    ->options(Ville::where('is_active', true)->pluck('nom', 'id'))
                    ->searchable(),

                TextInput::make('titre')
                    ->required()
                    ->maxLength(150),

                Select::make('type')
                    ->options([
                        'evenement' => 'Événement',
                        'emploi' => 'Emploi',
                        'restaurant' => 'Restaurant',
                        'pub' => 'Sortie',
                        'annonce' => 'Annonce',
                    ])
                    ->default('annonce')
                    ->required(),

                TextInput::make('categorie'),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('auteur'),
                TextInput::make('lieu'),

                // Champs texte libre (pas des vraies dates en base) : les
                // annonces utilisent des formulations comme "Chaque vendredi"
                // ou "Jusqu'au 28 février" plutôt qu'une date stricte.
                TextInput::make('date_debut')
                    ->label('Période / date de début')
                    ->helperText('Texte libre, ex. "15 février 2026" ou "Chaque vendredi".'),
                TextInput::make('date_fin')
                    ->label('Date de fin (optionnel)'),

                TextInput::make('contact'),
                TextInput::make('telephone')
                    ->tel()
                    ->helperText('Format libre : 07 98 24 05 15 ou +225 0798240515'),
                TextInput::make('email')->email(),
                TextInput::make('lien')->url()->label('Lien externe'),

                FileUpload::make('photo')
                    ->label('Photo de couverture')
                    ->image()
                    ->directory('annonces')
                    ->columnSpanFull(),

                FileUpload::make('photos')
                    ->label('Galerie')
                    ->helperText('Jusqu\'à 4 photos supplémentaires affichées dans la fiche.')
                    ->image()
                    ->multiple()
                    ->maxFiles(4)
                    ->reorderable()
                    ->directory('annonces/galerie')
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
