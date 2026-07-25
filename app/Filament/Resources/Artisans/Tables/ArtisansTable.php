<?php

namespace App\Filament\Resources\Artisans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArtisansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')->label(''),
                TextColumn::make('nom')->searchable()->weight('bold'),
                TextColumn::make('metier')->badge()->searchable(),
                TextColumn::make('ville.nom')->label('Ville'),
                TextColumn::make('telephone'),
                IconColumn::make('disponible')->boolean(),
                IconColumn::make('is_active')->label('Actif')->boolean(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
