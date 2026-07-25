<?php

namespace Database\Seeders;

use App\Models\Actualite;
use Illuminate\Database\Seeder;

class ActualiteSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['titre' => 'Réhabilitation de la route principale',  'categorie' => 'mairie',  'contenu' => "La mairie de Daoukro annonce le début des travaux de réhabilitation de l'avenue principale. Les travaux débuteront prochainement et dureront 3 mois. Des déviations seront mises en place."],
            ['titre' => 'Coupure d\'eau programmée',              'categorie' => 'alerte',  'contenu' => "La SODECI informe les habitants que l'alimentation en eau sera interrompue pour travaux de maintenance sur le réseau principal. Stockez de l'eau en prévision."],
            ['titre' => 'Journée de vaccination gratuite',        'categorie' => 'sante',   'contenu' => "L'hôpital général organise une journée de vaccination gratuite pour les enfants de 0 à 5 ans. Rendez-vous à partir de 8h. Carte de santé obligatoire."],
            ['titre' => 'Résultats du BEPC 2024',                 'categorie' => 'info',    'contenu' => "Le taux de réussite au BEPC dans la région de Daoukro atteint 68 %, en hausse de 5 points par rapport à l'année précédente. Félicitations aux lauréats."],
            ['titre' => 'Festival de musique traditionnelle',     'categorie' => 'culture', 'contenu' => "La ville de Daoukro accueille le festival annuel de musique traditionnelle. Trois jours de spectacles, expositions et animations dans les quartiers."],
        ];

        foreach ($data as $item) {
            Actualite::firstOrCreate(['titre' => $item['titre']], array_merge($item, ['is_active' => true]));
        }
    }
}
