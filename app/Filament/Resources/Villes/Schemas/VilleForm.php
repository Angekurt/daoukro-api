<?php

namespace App\Filament\Resources\Villes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VilleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom')
                    ->required(),
                TextInput::make('region'),
                TextInput::make('pays')
                    ->required()
                    ->default('Côte d\'Ivoire'),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
