<?php

namespace App\Filament\Resources\Avis\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AvisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')->searchable()->weight('bold'),
                TextColumn::make('entity_type')
                    ->label('Concerne')
                    ->formatStateUsing(fn (string $state) => $state === 'artisan' ? 'Artisan' : 'Hébergement')
                    ->badge(),
                TextColumn::make('note')->formatStateUsing(fn (int $state) => str_repeat('★', $state)),
                TextColumn::make('commentaire')->limit(50)->searchable(),
                TextColumn::make('statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'En attente',
                        'valide' => 'Validé',
                        'rejete' => 'Rejeté',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'valide' => 'success',
                        'rejete' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Déposé le')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('statut')->options([
                    'pending' => 'En attente',
                    'valide' => 'Validé',
                    'rejete' => 'Rejeté',
                ]),
                SelectFilter::make('entity_type')->label('Concerne')->options([
                    'artisan' => 'Artisan',
                    'hebergement' => 'Hébergement',
                ]),
            ])
            ->recordActions([
                Action::make('valider')
                    ->label('Valider')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->statut !== 'valide')
                    ->action(function ($record) {
                        $record->update(['statut' => 'valide']);
                        Notification::make()->title('Avis validé')->success()->send();
                    }),
                Action::make('rejeter')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->statut !== 'rejete')
                    ->action(function ($record) {
                        $record->update(['statut' => 'rejete']);
                        Notification::make()->title('Avis rejeté')->danger()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
