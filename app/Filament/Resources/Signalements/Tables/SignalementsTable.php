<?php

namespace App\Filament\Resources\Signalements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SignalementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('categorie')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'voirie' => 'Voirie',
                        'eclairage' => 'Éclairage',
                        'dechets' => 'Déchets',
                        'eau' => 'Eau',
                        'securite' => 'Sécurité',
                        default => 'Autre',
                    }),
                TextColumn::make('description')->limit(60)->searchable(),
                TextColumn::make('adresse'),
                TextColumn::make('auteur')->label('Signalé par')->placeholder('Anonyme'),
                TextColumn::make('statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'en_attente' => 'En attente',
                        'en_cours' => 'En cours',
                        'resolu' => 'Résolu',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'en_attente' => 'warning',
                        'en_cours' => 'info',
                        'resolu' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Reçu le')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('statut')->options([
                    'en_attente' => 'En attente',
                    'en_cours' => 'En cours',
                    'resolu' => 'Résolu',
                ]),
                SelectFilter::make('categorie')->options([
                    'voirie' => 'Voirie',
                    'eclairage' => 'Éclairage',
                    'dechets' => 'Déchets',
                    'eau' => 'Eau',
                    'securite' => 'Sécurité',
                    'autre' => 'Autre',
                ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
