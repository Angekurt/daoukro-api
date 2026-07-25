<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cle')
                    ->label('Clé')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText("Identifiant technique utilisé par l'application, ex. support_whatsapp."),
                TextInput::make('libelle')
                    ->label('Libellé')
                    ->required()
                    ->helperText('Nom lisible affiché ici, dans la liste.'),
                Select::make('groupe')
                    ->label('Groupe')
                    ->options([
                        'support' => 'Support',
                        'contenu' => 'Contenu',
                        'urgences' => 'Urgences',
                        'technique' => 'Technique',
                        'general' => 'Général',
                    ])
                    ->default('general')
                    ->required(),
                Textarea::make('valeur')
                    ->label('Valeur')
                    ->rows(4)
                    ->helperText('Modifiez cette valeur : elle est reflétée dans l\'application au prochain lancement, sans republier l\'APK.'),
            ]);
    }
}
