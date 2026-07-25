<?php

namespace App\Filament\Resources\Artisans\Schemas;

use App\Models\Ville;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ArtisanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ville_id')
                    ->label('Ville')
                    ->options(Ville::where('is_active', true)->pluck('nom', 'id'))
                    ->searchable(),

                TextInput::make('nom')
                    ->label('Nom')
                    ->required()
                    ->maxLength(150),

                TextInput::make('metier')
                    ->label('Métier')
                    ->required()
                    ->maxLength(100),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('telephone')
                    ->tel()
                    ->maxLength(20)
                    ->helperText('Format libre : 07 98 24 05 15 ou +225 0798240515'),

                TextInput::make('whatsapp')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('email')
                    ->email()
                    ->maxLength(150),

                TextInput::make('adresse')
                    ->maxLength(255),

                TextInput::make('latitude')
                    ->label('Latitude GPS')
                    ->numeric(),

                TextInput::make('longitude')
                    ->label('Longitude GPS')
                    ->numeric(),

                FileUpload::make('photo')
                    ->label('Photo de couverture')
                    ->image()
                    ->directory('artisans')
                    ->columnSpanFull(),

                FileUpload::make('photos')
                    ->label('Galerie (réalisations)')
                    ->helperText('Jusqu\'à 4 photos de réalisations affichées dans la fiche.')
                    ->image()
                    ->multiple()
                    ->maxFiles(4)
                    ->reorderable()
                    ->directory('artisans/galerie')
                    ->columnSpanFull(),

                Toggle::make('disponible')
                    ->label('Disponible actuellement')
                    ->default(true),

                Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),
            ]);
    }
}
