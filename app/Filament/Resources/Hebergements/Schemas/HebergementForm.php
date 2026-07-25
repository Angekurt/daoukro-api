<?php

namespace App\Filament\Resources\Hebergements\Schemas;

use App\Models\Ville;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HebergementForm
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

                Select::make('type')
                    ->options([
                        'hotel' => 'Hôtel',
                        'residence' => 'Résidence',
                        'meuble' => 'Meublé',
                        'auberge' => 'Auberge',
                    ])
                    ->default('hotel')
                    ->required(),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('adresse')
                    ->maxLength(255),

                TextInput::make('telephone')
                    ->tel()
                    ->maxLength(30)
                    ->helperText('Format libre : 07 98 24 05 15 ou +225 0798240515'),

                TextInput::make('whatsapp')
                    ->tel()
                    ->maxLength(30),

                TextInput::make('email')
                    ->email()
                    ->maxLength(150),

                TextInput::make('latitude')
                    ->label('Latitude GPS')
                    ->numeric(),

                TextInput::make('longitude')
                    ->label('Longitude GPS')
                    ->numeric(),

                TextInput::make('prix_min')
                    ->label('Prix minimum (FCFA/nuit)')
                    ->numeric(),

                TextInput::make('prix_max')
                    ->label('Prix maximum (FCFA/nuit)')
                    ->numeric(),

                FileUpload::make('photo')
                    ->label('Photo de couverture')
                    ->image()
                    ->directory('hebergements')
                    ->columnSpanFull(),

                FileUpload::make('photos')
                    ->label('Galerie (chambres, extérieur...)')
                    ->helperText('Jusqu\'à 4 photos supplémentaires affichées dans la fiche.')
                    ->image()
                    ->multiple()
                    ->maxFiles(4)
                    ->reorderable()
                    ->directory('hebergements/galerie')
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),
            ]);
    }
}
