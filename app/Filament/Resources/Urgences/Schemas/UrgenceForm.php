<?php

namespace App\Filament\Resources\Urgences\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UrgenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom')
                    ->required()
                    ->maxLength(150),

                Select::make('categorie')
                    ->options([
                        'sante' => 'Santé',
                        'securite' => 'Sécurité',
                        'incendie' => 'Incendie',
                        'autre' => 'Autre',
                    ])
                    ->required(),

                TextInput::make('telephone')
                    ->tel()
                    ->required()
                    ->helperText('Format libre : 07 98 24 05 15 ou +225 0798240515'),

                TextInput::make('telephone2')
                    ->label('Second numéro (optionnel)')
                    ->tel(),

                TextInput::make('adresse'),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),
            ]);
    }
}
