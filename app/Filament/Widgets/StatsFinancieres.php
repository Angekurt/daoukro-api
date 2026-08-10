<?php

namespace App\Filament\Widgets;

use App\Models\Citoyen;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

/**
 * Widget financier : revenus estimés, répartition des plans, expirations proches.
 * Les prix sont lus dynamiquement depuis les Settings (modifiables sans code).
 */
class StatsFinancieres extends StatsOverviewWidget
{
    // Visible uniquement par les admins
    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getStats(): array
    {
        // Charger les prix depuis les Settings
        $prix = [];
        foreach (['standard', 'pro', 'business'] as $plan) {
            $setting = Setting::where('cle', "plan_{$plan}")->first();
            $prix[$plan] = $setting ? (int)(json_decode($setting->valeur, true)['prix_fcfa'] ?? 0) : 0;
        }

        // Compter les abonnés par plan
        $counts = [];
        $revenuEstime = 0;
        foreach (['standard', 'pro', 'business'] as $plan) {
            $counts[$plan] = Citoyen::where('plan', $plan)
                ->where(function ($q) {
                    $q->whereNull('plan_expire_at')
                      ->orWhere('plan_expire_at', '>', now());
                })->count();
            $revenuEstime += $counts[$plan] * $prix[$plan];
        }

        // Abonnements expirant dans les 7 prochains jours
        $expirantBientot = Citoyen::whereIn('plan', ['standard', 'pro', 'business'])
            ->whereBetween('plan_expire_at', [now(), Carbon::now()->addDays(7)])
            ->count();

        // Nouveaux abonnements ce mois-ci
        $nouveauxAbonnes = Citoyen::whereIn('plan', ['standard', 'pro', 'business'])
            ->whereNotNull('plan_expire_at')
            ->where('plan_expire_at', '>', now())
            ->where('updated_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        return [
            Stat::make('Revenu mensuel estimé',
                    number_format($revenuEstime, 0, ',', ' ') . ' F CFA')
                ->description("Basé sur les abonnés actifs")
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Plan Standard actifs', $counts['standard'])
                ->description(number_format($prix['standard'], 0, ',', ' ') . ' F CFA/mois · ' .
                    number_format($counts['standard'] * $prix['standard'], 0, ',', ' ') . ' F est.')
                ->icon('heroicon-o-user')
                ->color('info'),

            Stat::make('Plan Pro actifs', $counts['pro'])
                ->description(number_format($prix['pro'], 0, ',', ' ') . ' F CFA/mois · ' .
                    number_format($counts['pro'] * $prix['pro'], 0, ',', ' ') . ' F est.')
                ->icon('heroicon-o-building-storefront')
                ->color('warning'),

            Stat::make('Plan Business actifs', $counts['business'])
                ->description(number_format($prix['business'], 0, ',', ' ') . ' F CFA/mois · ' .
                    number_format($counts['business'] * $prix['business'], 0, ',', ' ') . ' F est.')
                ->icon('heroicon-o-building-office')
                ->color('success'),

            Stat::make('Expirant dans 7 jours', $expirantBientot)
                ->description($expirantBientot > 0 ? 'Relancer ces clients' : 'Aucun')
                ->icon('heroicon-o-clock')
                ->color($expirantBientot > 0 ? 'warning' : 'gray'),

            Stat::make('Nouveaux abonnés ce mois', $nouveauxAbonnes)
                ->description('Plans payants activés')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('primary'),
        ];
    }
}
