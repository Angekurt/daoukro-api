<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('groupe')->badge()
                    ->color(fn ($state) => match($state) {
                        'plans'    => 'success',
                        'urgences' => 'danger',
                        'support'  => 'info',
                        'technique' => 'warning',
                        default    => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('libelle')->searchable()->weight('bold'),
                TextColumn::make('cle')->label('Clé')->searchable()->badge()->color('gray'),
                TextColumn::make('valeur')->limit(60)->searchable(),
                TextColumn::make('updated_at')->label('Modifié le')->dateTime()->sortable(),
            ])
            ->defaultSort('groupe')
            ->filters([
                SelectFilter::make('groupe')
                    ->label('Groupe')
                    ->options([
                        'plans'     => 'Plans tarifaires',
                        'support'   => 'Support',
                        'contenu'   => 'Contenu',
                        'urgences'  => 'Urgences',
                        'technique' => 'Technique',
                        'general'   => 'Général',
                    ]),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
