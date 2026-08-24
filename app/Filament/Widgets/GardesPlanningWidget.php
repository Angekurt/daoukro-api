<?php

namespace App\Filament\Widgets;

use App\Models\Garde;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class GardesPlanningWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Planning & Historique des Gardes de Pharmacie';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Garde::query()->with('pharmacie')->latest('date_debut')
            )
            ->columns([
                TextColumn::make('pharmacie.nom')
                    ->label('Pharmacie')
                    ->icon('heroicon-o-building-office-2')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('date_debut')
                    ->label('Date de Début')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('date_fin')
                    ->label('Date de Fin')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('statut')
                    ->label('Statut de la garde')
                    ->badge()
                    ->state(function (Garde $record): string {
                        $today = Carbon::today();
                        $debut = Carbon::parse($record->date_debut);
                        $fin = Carbon::parse($record->date_fin);

                        if ($today->between($debut, $fin)) {
                            return 'En cours de garde';
                        }
                        if ($today->lt($debut)) {
                            $diff = $today->diffInDays($debut);
                            return $diff === 1 ? 'Demain' : "Dans {$diff} jours";
                        }
                        return 'Garde terminée';
                    })
                    ->color(function (Garde $record): string {
                        $today = Carbon::today();
                        $debut = Carbon::parse($record->date_debut);
                        $fin = Carbon::parse($record->date_fin);

                        if ($today->between($debut, $fin)) {
                            return 'success';
                        }
                        if ($today->lt($debut)) {
                            return 'info';
                        }
                        return 'gray';
                    })
                    ->icon(function (Garde $record): string {
                        $today = Carbon::today();
                        $debut = Carbon::parse($record->date_debut);
                        $fin = Carbon::parse($record->date_fin);

                        if ($today->between($debut, $fin)) {
                            return 'heroicon-o-check-circle';
                        }
                        if ($today->lt($debut)) {
                            return 'heroicon-o-clock';
                        }
                        return 'heroicon-o-archive-box';
                    }),

                TextColumn::make('note')
                    ->label('Note / Précision')
                    ->placeholder('Aucune note')
                    ->limit(40),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
