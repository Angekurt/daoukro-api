<?php

namespace Database\Seeders;

use App\Models\Hebergement;
use Illuminate\Database\Seeder;

class HebergementSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nom' => 'Hôtel Le Président',    'type' => 'hotel',     'description' => 'Hôtel de standing au coeur de Daoukro. Climatisation, restaurant, parking sécurisé.',     'adresse' => 'Avenue Principale, Daoukro',    'telephone' => '+22527000001', 'prix_min' => 15000, 'prix_max' => 35000, 'note' => 4.2, 'nb_avis' => 38],
            ['nom' => 'Résidence Les Palmiers', 'type' => 'residence', 'description' => 'Appartements meublés pour courts et longs séjours. Cuisine équipée, WiFi inclus.',          'adresse' => 'Quartier Résidentiel, Daoukro', 'telephone' => '+22527000002', 'prix_min' => 10000, 'prix_max' => 20000, 'note' => 4.0, 'nb_avis' => 22],
            ['nom' => 'Auberge du Voyageur',    'type' => 'hotel',     'description' => 'Hébergement simple et propre. Idéal pour les voyageurs de passage.',                       'adresse' => 'Près de la gare routière',      'telephone' => '+22527000003', 'prix_min' =>  5000, 'prix_max' => 10000, 'note' => 3.5, 'nb_avis' => 15],
            ['nom' => 'Villa Confort',           'type' => 'meuble',    'description' => 'Villa 3 chambres entièrement meublée. Idéale pour familles ou groupes.',                  'adresse' => 'Zone Résidentielle Nord',       'telephone' => '+22527000004', 'prix_min' => 25000, 'prix_max' => 50000, 'note' => 4.7, 'nb_avis' =>  9],
        ];

        foreach ($data as $item) {
            Hebergement::firstOrCreate(['nom' => $item['nom']], array_merge($item, ['is_active' => true]));
        }
    }
}
