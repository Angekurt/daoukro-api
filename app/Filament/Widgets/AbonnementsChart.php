<?php

namespace App\Filament\Widgets;

use App\Models\Citoyen;
use App\Models\Setting;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

/**
 * Évolution des abonnements payants sur les 6 derniers mois.
 * Visible uniquement par les admins.
 */
class AbonnementsChart extends ChartWidget
{
    protected ?string $heading = 'Abonnements payants — 6 derniers mois';
    protected ?string $description = 'Standard, Pro et Business';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getData(): array
    {
        $mois = collect(range(5, 0))->map(
            fn ($i) => Carbon::now()->startOfMonth()->subMonths($i)
        );

        $datasets = [];
        $couleurs = [
            'standard' => ['border' => '#2C5F8A', 'bg' => 'rgba(44,95,138,0.15)'],
            'pro'      => ['border' => '#EF8A0C', 'bg' => 'rgba(239,138,12,0.15)'],
            'business' => ['border' => '#145217', 'bg' => 'rgba(20,82,23,0.15)'],
        ];

        foreach (['standard', 'pro', 'business'] as $plan) {
            $setting = Setting::where('cle', "plan_{$plan}")->first();
            $nomPlan = $setting ? (json_decode($setting->valeur, true)['nom'] ?? ucfirst($plan)) : ucfirst($plan);

            $counts = Citoyen::where('plan', $plan)
                ->selectRaw("DATE_FORMAT(updated_at, '%Y-%m') as mois, COUNT(*) as total")
                ->where('updated_at', '>=', Carbon::now()->startOfMonth()->subMonths(5))
                ->groupBy('mois')
                ->pluck('total', 'mois');

            $datasets[] = [
                'label'           => $nomPlan,
                'data'            => $mois->map(fn ($m) => $counts[$m->format('Y-m')] ?? 0)->all(),
                'borderColor'     => $couleurs[$plan]['border'],
                'backgroundColor' => $couleurs[$plan]['bg'],
                'fill'            => true,
                'tension'         => 0.4,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels'   => $mois->map(fn ($m) => $m->locale('fr')->isoFormat('MMM YY'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
