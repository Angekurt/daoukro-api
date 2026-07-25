<?php

namespace App\Filament\Widgets;

use App\Models\Actualite;
use App\Models\Annonce;
use App\Models\Artisan;
use App\Models\Hebergement;
use App\Models\Immobilier;
use App\Models\Pharmacie;
use App\Models\ServicePublic;
use App\Models\Urgence;
use Filament\Widgets\ChartWidget;

class RepartitionContenuChart extends ChartWidget
{
    protected ?string $heading = 'Répartition du contenu';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => [
                        Pharmacie::count(),
                        ServicePublic::count(),
                        Hebergement::count(),
                        Immobilier::count(),
                        Artisan::count(),
                        Annonce::count(),
                        Urgence::count(),
                        Actualite::count(),
                    ],
                    'backgroundColor' => [
                        '#2E7D32', '#145217', '#6B4E9E', '#2C5F8A',
                        '#EF8A0C', '#EF8A0C', '#B3261E', '#0C3810',
                    ],
                ],
            ],
            'labels' => [
                'Pharmacies', 'Services publics', 'Hébergements', 'Immobilier',
                'Artisans', 'Annonces', 'Urgences', 'Actualités',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
