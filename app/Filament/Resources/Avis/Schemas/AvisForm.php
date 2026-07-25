<?php

namespace App\Filament\Resources\Avis\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AvisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nom')->disabled(),
            TextInput::make('entity_type')->label('Concerne')->disabled(),
            TextInput::make('entity_id')->label('ID de la fiche')->disabled(),
            TextInput::make('note')->numeric()->disabled(),
            Textarea::make('commentaire')->disabled()->columnSpanFull(),
            Select::make('statut')
                ->options([
                    'pending' => 'En attente',
                    'valide' => 'Validé (visible dans l\'app)',
                    'rejete' => 'Rejeté (masqué)',
                ])
                ->required(),
        ]);
    }
}
