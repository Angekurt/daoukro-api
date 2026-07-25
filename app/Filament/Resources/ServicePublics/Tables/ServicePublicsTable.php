<?php

namespace App\Filament\Resources\ServicePublics\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServicePublicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular(),

                TextColumn::make('nom')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('categorie.nom')
                    ->label('Catégorie')
                    ->badge()
                    ->sortable(),

                TextColumn::make('ville.nom')
                    ->label('Ville')
                    ->sortable(),

                TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->icon('heroicon-o-phone'),

                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Services actifs'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
