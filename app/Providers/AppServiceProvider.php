<?php

namespace App\Providers;

use App\Models\Actualite;
use App\Models\Annonce;
use App\Models\Artisan;
use App\Models\Hebergement;
use App\Models\Immobilier;
use App\Observers\FicheObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Observer unique sur les 5 modèles — écoute is_active pour les
        // emails de validation et les pushs publiques.
        Artisan::observe(FicheObserver::class);
        Hebergement::observe(FicheObserver::class);
        Immobilier::observe(FicheObserver::class);
        Annonce::observe(FicheObserver::class);
        Actualite::observe(FicheObserver::class);
    }
}
