<?php

namespace App\Filament\Resources\Immobiliers\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ImmobiliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')->label(''),
                TextColumn::make('titre')->searchable()->weight('bold'),
                TextColumn::make('type_offre')->badge(),
                TextColumn::make('type_bien')->badge(),
                TextColumn::make('citoyen.name')->label('Soumis par')->placeholder('—'),
                TextColumn::make('prix')->numeric()->sortable(),
                IconColumn::make('is_active')->label('Publié')->boolean(),
                TextColumn::make('motif_rejet')->label('Motif rejet')->placeholder('—')
                    ->limit(40)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Statut')
                    ->trueLabel('Publiés')->falseLabel('En attente'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('valider')
                    ->label('Valider')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn ($record) => ! $record->is_active)
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['is_active' => true, 'motif_rejet' => null])),
                Action::make('rejeter')
                    ->label('Rejeter')->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn ($record) => $record->is_active || ! $record->motif_rejet)
                    ->form([
                        Textarea::make('motif_rejet')
                            ->label('Motif du refus')->required()->maxLength(500)->rows(3),
                    ])
                    ->action(fn ($record, array $data) => $record->update([
                        'is_active' => false, 'motif_rejet' => $data['motif_rejet'],
                    ]))
                    ->after(fn () => Notification::make()->title('Bien immobilier rejeté')->warning()->send()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
