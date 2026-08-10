<?php

namespace App\Filament\Widgets;

use App\Models\Annonce;
use App\Models\Artisan;
use App\Models\Avis;
use App\Models\Citoyen;
use App\Models\Hebergement;
use App\Models\Immobilier;
use App\Models\Signalement;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ActiviteChart extends ChartWidget
{
    protected ?string $heading = 'Activité des 30 derniers jours';
    protected ?string $description = 'Fiches soumises, avis reçus, signalements, nouveaux comptes';

    public string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7'  => '7 derniers jours',
            '30' => '30 derniers jours',
            '90' => '3 derniers mois',
        ];
    }

    protected function getData(): array
    {
        $jours = (int) $this->filter;
        $periode = collect(range($jours - 1, 0))->map(
            fn ($i) => Carbon::today()->subDays($i)
        );

        $groupBy = $jours <= 30 ? 'DATE(created_at)' : 'DATE(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY))';
        $format  = $jours <= 30 ? 'd/m' : 'd/m';

        // Fiches soumises (toutes catégories confondues)
        $fichesCounts = $this->compterParJour(
            array_merge(
                $this->getCountsParJour(Artisan::class, $jours),
                $this->getCountsParJour(Hebergement::class, $jours),
                $this->getCountsParJour(Immobilier::class, $jours),
                $this->getCountsParJour(Annonce::class, $jours),
            ),
            $periode
        );

        // Avis reçus
        $avisCounts = $this->getCountsParJourSimple(Avis::class, $jours, $periode);

        // Signalements
        $signalCounts = $this->getCountsParJourSimple(Signalement::class, $jours, $periode);

        // Nouveaux comptes pros
        $compteCounts = $this->getCountsParJourSimple(Citoyen::class, $jours, $periode);

        return [
            'datasets' => [
                [
                    'label'           => 'Fiches soumises',
                    'data'            => $fichesCounts,
                    'borderColor'     => '#145217',
                    'backgroundColor' => 'rgba(20,82,23,0.15)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Avis reçus',
                    'data'            => $avisCounts,
                    'borderColor'     => '#EF8A0C',
                    'backgroundColor' => 'rgba(239,138,12,0.1)',
                    'fill'            => false,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Signalements',
                    'data'            => $signalCounts,
                    'borderColor'     => '#B3261E',
                    'backgroundColor' => 'rgba(179,38,30,0.1)',
                    'fill'            => false,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Nouveaux comptes',
                    'data'            => $compteCounts,
                    'borderColor'     => '#2C5F8A',
                    'backgroundColor' => 'rgba(44,95,138,0.1)',
                    'fill'            => false,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $periode->map(fn ($j) => $j->format($format))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getCountsParJour(string $model, int $jours): array
    {
        return $model::selectRaw('DATE(created_at) as jour, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::today()->subDays($jours - 1))
            ->whereNotNull('citoyen_id') // uniquement soumissions depuis la PWA
            ->groupBy('jour')
            ->pluck('total', 'jour')
            ->toArray();
    }

    private function getCountsParJourSimple(string $model, int $jours, $periode): array
    {
        $counts = $model::selectRaw('DATE(created_at) as jour, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::today()->subDays($jours - 1))
            ->groupBy('jour')
            ->pluck('total', 'jour');

        return $periode->map(fn ($j) => $counts[$j->format('Y-m-d')] ?? 0)->all();
    }

    private function compterParJour(array $merged, $periode): array
    {
        $totaux = [];
        foreach ($merged as $jour => $total) {
            $totaux[$jour] = ($totaux[$jour] ?? 0) + $total;
        }
        return $periode->map(fn ($j) => $totaux[$j->format('Y-m-d')] ?? 0)->all();
    }
}
