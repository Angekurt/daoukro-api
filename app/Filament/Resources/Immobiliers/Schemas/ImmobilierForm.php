<?php

namespace App\Filament\Resources\Immobiliers\Schemas;

use App\Models\Ville;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ImmobilierForm
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

                Select::make('type_offre')
                    ->options(['vente' => 'Vente', 'location' => 'Location'])
                    ->required(),

                Select::make('type_bien')
                    ->options([
                        'maison' => 'Maison',
                        'terrain' => 'Terrain',
                        'appartement' => 'Appartement',
                        'villa' => 'Villa',
                    ])
                    ->required(),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('adresse'),
                TextInput::make('quartier'),

                TextInput::make('prix')
                    ->label('Prix (FCFA)')
                    ->numeric()
                    ->required(),

                TextInput::make('surface')
                    ->helperText('Ex : 500 m²'),

                TextInput::make('nb_chambres')
                    ->label('Nombre de chambres')
                    ->numeric(),

                TextInput::make('telephone')
                    ->tel()
                    ->helperText('Format libre : 07 98 24 05 15 ou +225 0798240515'),

                TextInput::make('whatsapp')->tel(),
                TextInput::make('email')->email(),

                TextInput::make('latitude')->numeric(),
                TextInput::make('longitude')->numeric(),

                FileUpload::make('photo')
                    ->label('Photo de couverture')
                    ->image()
                    ->directory('immobilier')
                    ->columnSpanFull(),

                FileUpload::make('photos')
                    ->label('Galerie (pièces, extérieur...)')
                    ->helperText('Jusqu\'à 4 photos supplémentaires affichées dans la fiche.')
                    ->image()
                    ->multiple()
                    ->maxFiles(4)
                    ->reorderable()
                    ->directory('immobilier/galerie')
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),
            ]);
    }
}
