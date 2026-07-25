<?php

namespace Database\Seeders;

use App\Models\Immobilier;
use Illuminate\Database\Seeder;

class ImmobilierSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['titre' => 'Villa F4 à vendre',        'type_offre' => 'vente',    'type_bien' => 'villa',       'description' => 'Belle villa 4 pièces avec jardin. Titre foncier disponible.',           'adresse' => 'Quartier Résidentiel', 'quartier' => 'Zone Nord',      'prix' => 18000000, 'surface' => '200 m²', 'nb_chambres' => 4, 'telephone' => '+22507100001'],
            ['titre' => 'Terrain 500m² à vendre',   'type_offre' => 'vente',    'type_bien' => 'terrain',     'description' => 'Terrain viabilisé, bornage effectué. Accès route bitumée.',             'adresse' => "Zone d'extension",     'quartier' => 'Périphérie Est', 'prix' =>  3500000, 'surface' => '500 m²', 'nb_chambres' => null, 'telephone' => '+22507100002'],
            ['titre' => 'Appartement F3 à louer',   'type_offre' => 'location', 'type_bien' => 'appartement', 'description' => 'Appartement 3 pièces au 2ème étage. Eau et électricité disponibles.',  'adresse' => 'Centre-ville',         'quartier' => 'Centre',         'prix' =>    60000, 'surface' => '80 m²',  'nb_chambres' => 2, 'telephone' => '+22507100003'],
            ['titre' => 'Maison F5 à louer',         'type_offre' => 'location', 'type_bien' => 'maison',      'description' => 'Grande maison familiale avec cour. Idéale pour famille nombreuse.',    'adresse' => 'Quartier Calme',       'quartier' => 'Zone Ouest',     'prix' =>   120000, 'surface' => '150 m²', 'nb_chambres' => 5, 'telephone' => '+22507100004'],
        ];

        foreach ($data as $item) {
            Immobilier::firstOrCreate(['titre' => $item['titre']], array_merge($item, ['is_active' => true]));
        }
    }
}
