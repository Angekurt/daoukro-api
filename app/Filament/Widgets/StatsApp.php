<?php

namespace App\Filament\Widgets;

use App\Models\Artisan;
use App\Models\Avis;
use App\Models\FcmToken;
use App\Models\Hebergement;
use App\Models\Pharmacie;
use App\Models\Prestataire;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsApp extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $candidatures = Prestataire::pending()->count();
        $avisEnAttente = Avis::pending()->count();

        return [
            Stat::make('Pharmacies actives', Pharmacie::where('is_active', true)->count())
                ->icon('heroicon-o-plus-circle')
                ->color('success'),

            Stat::make('Artisans actifs', Artisan::where('is_active', true)->count())
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('success'),

            Stat::make('Hébergements', Hebergement::where('is_active', true)->count())
                ->icon('heroicon-o-building-office-2')
                ->color('success'),

            Stat::make('Candidatures en attente', $candidatures)
                ->description($candidatures > 0 ? 'À traiter' : 'Rien en attente')
                ->icon('heroicon-o-identification')
                ->color($candidatures > 0 ? 'warning' : 'gray'),

            Stat::make('Avis en attente', $avisEnAttente)
                ->description($avisEnAttente > 0 ? 'À modérer' : 'Rien en attente')
                ->icon('heroicon-o-star')
                ->color($avisEnAttente > 0 ? 'warning' : 'gray'),

            Stat::make('Appareils avec l\'app installée', FcmToken::count())
                ->description('Notifications activées')
                ->icon('heroicon-o-device-phone-mobile')
                ->color('primary'),
        ];
    }
}
