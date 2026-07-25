<?php

namespace App\Filament\Resources\Pharmacies\Schemas;

use App\Models\Ville;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PharmacieForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ville_id')
                    ->label('Ville')
                    ->options(Ville::where('is_active', true)->pluck('nom', 'id'))
                    ->required()
                    ->searchable(),

                TextInput::make('nom')
                    ->label('Nom de la pharmacie')
                    ->required()
                    ->maxLength(150),

                TextInput::make('adresse')
                    ->label('Adresse')
                    ->required()
                    ->maxLength(255),

                TextInput::make('telephone')
                    ->label('Téléphone')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('latitude')
                    ->label('Latitude GPS')
                    ->numeric(),

                TextInput::make('longitude')
                    ->label('Longitude GPS')
                    ->numeric(),

                Textarea::make('horaires')
                    ->label('Horaires d\'ouverture')
                    ->rows(3)
                    ->columnSpanFull(),

                FileUpload::make('photo')
                    ->label('Photo de couverture')
                    ->image()
                    ->directory('pharmacies')
                    ->columnSpanFull(),

                FileUpload::make('photos')
                    ->label('Galerie (façade, intérieur...)')
                    ->helperText('Jusqu\'à 4 photos supplémentaires affichées dans la fiche.')
                    ->image()
                    ->multiple()
                    ->maxFiles(4)
                    ->reorderable()
                    ->directory('pharmacies/galerie')
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Pharmacie active')
                    ->default(true),
            ]);
    }
}
