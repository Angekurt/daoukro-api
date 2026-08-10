<?php

namespace App\Filament\Pages;

use App\Models\Citoyen;
use App\Models\Setting;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Rapport financier : liste tous les abonnés actifs avec leurs détails
 * et un résumé du revenu estimé.
 * Réservé aux admins.
 */
class RapportPaiements extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.rapport-paiements';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Rapport financier';

    protected static ?string $title = 'Rapport financier & Abonnements';

    protected static ?int $navigationSort = 11;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    // Résumé financier pour la vue
    public function getRevenuEstime(): string
    {
        $total = 0;
        foreach (['standard', 'pro', 'business'] as $plan) {
            $setting = Setting::where('cle', "plan_{$plan}")->first();
            $prix = $setting ? (int)(json_decode($setting->valeur, true)['prix_fcfa'] ?? 0) : 0;
            $nb = Citoyen::where('plan', $plan)
                ->where(fn ($q) => $q->whereNull('plan_expire_at')->orWhere('plan_expire_at', '>', now()))
                ->count();
            $total += $prix * $nb;
        }
        return number_format($total, 0, ',', ' ') . ' F CFA';
    }

    public function getNbAbonnes(): int
    {
        return Citoyen::whereIn('plan', ['standard', 'pro', 'business'])
            ->where(fn ($q) => $q->whereNull('plan_expire_at')->orWhere('plan_expire_at', '>', now()))
            ->count();
    }

    public function getNbExpirantBientot(): int
    {
        return Citoyen::whereIn('plan', ['standard', 'pro', 'business'])
            ->whereBetween('plan_expire_at', [now(), Carbon::now()->addDays(7)])
            ->count();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Citoyen::query()
                    ->whereIn('plan', ['standard', 'pro', 'business'])
                    ->orderByDesc('plan_expire_at')
            )
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->weight('bold')
                    ->description(fn ($record) => $record->email ?? $record->telephone ?? '—'),
                TextColumn::make('plan')->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'standard' => 'Standard', 'pro' => 'Pro',
                        'business' => 'Business', default => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'standard' => 'info', 'pro' => 'warning',
                        'business' => 'success', default => 'gray',
                    }),
                TextColumn::make('plan_expire_at')->label('Expiration')
                    ->date('d/m/Y')
                    ->description(fn ($record) => $record->plan_expire_at?->isPast()
                        ? 'Expiré'
                        : ($record->plan_expire_at?->diffInDays(now()) <= 7
                            ? 'Expire bientôt'
                            : null))
                    ->color(fn ($record) => $record->plan_expire_at?->isPast()
                        ? 'danger'
                        : ($record->plan_expire_at?->diffInDays(now()) <= 7 ? 'warning' : null))
                    ->sortable(),
                TextColumn::make('auth_provider')->label('Auth')->badge()
                    ->formatStateUsing(fn ($s) => $s === 'google' ? 'Google' : 'Email'),
                TextColumn::make('created_at')->label('Inscrit le')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('plan')->options([
                    'standard' => 'Standard',
                    'pro'      => 'Pro',
                    'business' => 'Business',
                ]),
                SelectFilter::make('expire')
                    ->label('Statut expiration')
                    ->options([
                        'actif'   => 'Actif',
                        'bientot' => 'Expire dans 7 jours',
                        'expire'  => 'Expiré',
                    ])
                    ->query(fn (Builder $q, array $data) => match($data['value'] ?? null) {
                        'actif'   => $q->where('plan_expire_at', '>', now()),
                        'bientot' => $q->whereBetween('plan_expire_at', [now(), Carbon::now()->addDays(7)]),
                        'expire'  => $q->where('plan_expire_at', '<', now()),
                        default   => $q,
                    }),
            ])
            ->defaultSort('plan_expire_at', 'asc');
    }
}
