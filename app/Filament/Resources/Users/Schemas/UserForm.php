<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(150),

                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(150),

                Select::make('role')
                    ->label('Rôle')
                    ->options([
                        'admin' => 'Administrateur (accès complet)',
                        'moderateur' => 'Modérateur (gestion du contenu au quotidien)',
                    ])
                    ->default('moderateur')
                    ->required()
                    ->helperText("L'administrateur (DG) est seul à pouvoir supprimer des fiches, gérer les comptes et modifier les paramètres de l'application."),

                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->helperText('Laisser vide pour ne pas changer le mot de passe (en modification).'),
            ]);
    }
}
