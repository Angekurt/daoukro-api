<?php

namespace App\Filament\Widgets;

use App\Models\Avis;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ActiviteChart extends ChartWidget
{
    protected ?string $heading = 'Avis déposés (14 derniers jours)';

    protected function getData(): array
    {
        $jours = collect(range(13, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $counts = Avis::selectRaw('DATE(created_at) as jour, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::today()->subDays(13))
            ->groupBy('jour')
            ->pluck('total', 'jour');

        return [
            'datasets' => [
                [
                    'label' => 'Avis reçus',
                    'data' => $jours->map(fn ($j) => $counts[$j->format('Y-m-d')] ?? 0)->all(),
                    'borderColor' => '#145217',
                    'backgroundColor' => 'rgba(20, 82, 23, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $jours->map(fn ($j) => $j->format('d/m'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
