<?php

namespace App\Filament\Resources\ServicePublics\Schemas;

use App\Models\CategorieService;
use App\Models\Ville;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServicePublicForm
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

                Select::make('categorie_id')
                    ->label('Catégorie')
                    ->options(CategorieService::pluck('nom', 'id'))
                    ->required()
                    ->searchable(),

                TextInput::make('nom')
                    ->label('Nom du service')
                    ->required()
                    ->maxLength(150),

                TextInput::make('telephone')
                    ->label('Téléphone')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(150),

                TextInput::make('adresse')
                    ->label('Adresse')
                    ->maxLength(255),

                TextInput::make('latitude')
                    ->label('Latitude GPS')
                    ->numeric(),

                TextInput::make('longitude')
                    ->label('Longitude GPS')
                    ->numeric(),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('horaires')
                    ->label('Horaires')
                    ->rows(2)
                    ->columnSpanFull(),

                FileUpload::make('photo')
                    ->label('Photo de couverture')
                    ->image()
                    ->directory('services')
                    ->columnSpanFull(),

                FileUpload::make('photos')
                    ->label('Galerie')
                    ->helperText('Jusqu\'à 4 photos supplémentaires affichées dans la fiche.')
                    ->image()
                    ->multiple()
                    ->maxFiles(4)
                    ->reorderable()
                    ->directory('services/galerie')
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Service actif')
                    ->default(true),
            ]);
    }
}
