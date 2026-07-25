<?php

namespace App\Filament\Resources\Prestataires\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PrestatairesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom_complet')->searchable()->weight('bold'),
                TextColumn::make('categorieMetier.nom')->label('Métier'),
                TextColumn::make('telephone'),
                TextColumn::make('ville.nom')->label('Ville'),
                TextColumn::make('statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Candidature le')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('statut')->options([
                    'pending' => 'En attente',
                    'approved' => 'Approuvé',
                    'rejected' => 'Rejeté',
                ]),
            ])
            ->recordActions([
                Action::make('approuver')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->statut === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['statut' => 'approved', 'approved_at' => now()]);
                        Notification::make()->title('Candidature approuvée')->success()->send();
                    }),
                Action::make('rejeter')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->statut === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['statut' => 'rejected', 'rejected_at' => now()]);
                        Notification::make()->title('Candidature rejetée')->danger()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
