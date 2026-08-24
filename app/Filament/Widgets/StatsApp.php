<?php

namespace App\Filament\Widgets;

use App\Models\Annonce;
use App\Models\AppDevice;
use App\Models\Artisan;
use App\Models\Avis;
use App\Models\Citoyen;
use App\Models\FcmToken;
use App\Models\Garde;
use App\Models\Hebergement;
use App\Models\Immobilier;
use App\Models\Pharmacie;
use App\Models\Signalement;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsApp extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $avisEnAttente = Avis::pending()->count();
        $signalEnAttente = Signalement::where('statut', 'en_attente')->count();

        $fichesEnAttente = Artisan::where('is_active', false)->whereNotNull('citoyen_id')->count()
            + Hebergement::where('is_active', false)->whereNotNull('citoyen_id')->count()
            + Immobilier::where('is_active', false)->whereNotNull('citoyen_id')->count()
            + Annonce::where('is_active', false)->whereNotNull('citoyen_id')->count();

        $nouveauxComptesMonth = Citoyen::where('created_at', '>=', Carbon::now()->startOfMonth())->count();

        $abonnesActifs = Citoyen::whereIn('plan', ['standard', 'pro', 'business'])
            ->where(function ($q) {
                $q->whereNull('plan_expire_at')->orWhere('plan_expire_at', '>', now());
            })->count();

        // Statistiques appareils et installations (anti-doublons)
        $totalDevices = AppDevice::count();
        $androidDevices = AppDevice::where('platform', 'android')->count();
        $iosPwaDevices = AppDevice::where('platform', 'ios_pwa')->count();
        $pushActifs = AppDevice::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->count();

        // Si la table app_devices est vide mais que fcm_tokens contient d'anciens enregistrements
        if ($totalDevices === 0 && FcmToken::count() > 0) {
            $totalDevices = FcmToken::count();
            $androidDevices = $totalDevices;
            $pushActifs = $totalDevices;
        }

        // Statistiques des pharmacies et gardes actives
        $gardesActives = Garde::actives()->with('pharmacie')->get();
        $nbGardes = $gardesActives->count();
        $totalPharmacies = Pharmacie::where('is_active', true)->count();

        return [
            Stat::make('Appareils uniques', $totalDevices)
                ->description("Téléphones uniques enregistrés")
                ->icon('heroicon-o-device-phone-mobile')
                ->color('primary'),

            Stat::make('Android (APK)', $androidDevices)
                ->description('Installations APK Android')
                ->icon('heroicon-o-cpu-chip')
                ->color('success'),

            Stat::make('iOS (PWA)', $iosPwaDevices)
                ->description('Installations PWA iOS Safari')
                ->icon('heroicon-o-globe-alt')
                ->color('info'),

            Stat::make('Notifications push actives', $pushActifs)
                ->description('Appareils avec push activé')
                ->icon('heroicon-o-bell')
                ->color('warning'),

            Stat::make('Fiches en attente', $fichesEnAttente)
                ->description($fichesEnAttente > 0 ? 'À valider ou rejeter' : 'Tout est traité')
                ->icon('heroicon-o-document-check')
                ->color($fichesEnAttente > 0 ? 'warning' : 'success'),

            Stat::make('Artisans publiés', Artisan::where('is_active', true)->count())
                ->description("Dans l'app mobile")
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('success'),

            Stat::make('Hébergements publiés', Hebergement::where('is_active', true)->count())
                ->description("Dans l'app mobile")
                ->icon('heroicon-o-building-office-2')
                ->color('success'),

            Stat::make('Biens immobiliers', Immobilier::where('is_active', true)->count())
                ->description('Publiés dans l\'app')
                ->icon('heroicon-o-home')
                ->color('success'),

            Stat::make('Annonces actives', Annonce::where('is_active', true)->count())
                ->description('Publiées dans l\'app')
                ->icon('heroicon-o-megaphone')
                ->color('success'),

            Stat::make('Pharmacies de garde', $nbGardes > 0 ? "{$nbGardes} active(s)" : '0 de garde')
                ->description($nbGardes > 0 ? ($gardesActives->pluck('pharmacie.nom')->filter()->implode(', ') ?: 'En service aujourd\'hui') : '⚠️ Aucune pharmacie de garde aujourd\'hui !')
                ->icon($nbGardes > 0 ? 'heroicon-o-plus-circle' : 'heroicon-o-exclamation-triangle')
                ->color($nbGardes > 0 ? 'success' : 'danger'),

            Stat::make('Total Pharmacies', $totalPharmacies)
                ->description('Établissements référencés à Daoukro')
                ->icon('heroicon-o-building-office-2')
                ->color('info'),

            Stat::make('Comptes pros', Citoyen::count())
                ->description("+{$nouveauxComptesMonth} ce mois-ci")
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Abonnés payants', $abonnesActifs)
                ->description('Plans Standard / Pro / Business actifs')
                ->icon('heroicon-o-credit-card')
                ->color($abonnesActifs > 0 ? 'success' : 'gray'),

            Stat::make('Avis à modérer', $avisEnAttente)
                ->description($avisEnAttente > 0 ? 'En attente de validation' : 'Tout est traité')
                ->icon('heroicon-o-star')
                ->color($avisEnAttente > 0 ? 'warning' : 'gray'),

            Stat::make('Signalements en attente', $signalEnAttente)
                ->description($signalEnAttente > 0 ? 'À traiter' : 'Aucun en attente')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($signalEnAttente > 0 ? 'danger' : 'gray'),
        ];
    }
}
