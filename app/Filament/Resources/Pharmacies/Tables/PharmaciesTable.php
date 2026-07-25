<?php

namespace App\Filament\Resources\Pharmacies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PharmaciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular(),

                TextColumn::make('nom')
                    ->label('Pharmacie')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ville.nom')
                    ->label('Ville')
                    ->sortable(),

                TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->icon('heroicon-o-phone'),

                TextColumn::make('gardes_count')
                    ->label('Gardes')
                    ->counts('gardes')
                    ->badge(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Pharmacies actives'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
