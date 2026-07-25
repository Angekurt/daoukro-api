<?php

namespace Database\Seeders;

use App\Models\Artisan;
use Illuminate\Database\Seeder;

class ArtisanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nom' => 'Koné Ibrahim',    'metier' => 'Plombier',    'description' => 'Plombier qualifié, 10 ans d\'expérience. Intervention rapide.',            'telephone' => '+22507200001', 'whatsapp' => '+22507200001', 'adresse' => 'Daoukro Centre',          'note' => 4.5, 'nb_avis' => 28, 'disponible' => true],
            ['nom' => 'Yao Kouassi',     'metier' => 'Électricien', 'description' => 'Installation et dépannage électrique. Devis gratuit.',                     'telephone' => '+22507200002', 'whatsapp' => '+22507200002', 'adresse' => 'Daoukro',                 'note' => 4.3, 'nb_avis' => 19, 'disponible' => true],
            ['nom' => 'Coulibaly Seydou','metier' => 'Maçon',       'description' => 'Construction, rénovation, carrelage. Travail soigné et rapide.',           'telephone' => '+22507200003', 'whatsapp' => null,           'adresse' => 'Daoukro',                 'note' => 4.1, 'nb_avis' => 34, 'disponible' => false],
            ['nom' => 'Adjoua Marie',    'metier' => 'Couturière',  'description' => 'Couture sur mesure, retouches, tenues traditionnelles et modernes.',       'telephone' => '+22507200004', 'whatsapp' => '+22507200004', 'adresse' => 'Marché Central, Daoukro', 'note' => 4.8, 'nb_avis' => 52, 'disponible' => true],
            ['nom' => 'Traoré Moussa',   'metier' => 'Menuisier',   'description' => 'Fabrication meubles sur mesure, portes, fenêtres.',                       'telephone' => '+22507200005', 'whatsapp' => null,           'adresse' => 'Zone Artisanale',         'note' => 4.0, 'nb_avis' => 17, 'disponible' => true],
            ['nom' => 'Bamba Fatou',     'metier' => 'Coiffeuse',   'description' => 'Coiffure africaine et moderne. Tresses, tissages, soins capillaires.',     'telephone' => '+22507200006', 'whatsapp' => '+22507200006', 'adresse' => 'Quartier Résidentiel',    'note' => 4.6, 'nb_avis' => 41, 'disponible' => true],
            ['nom' => 'Diallo Mamadou',  'metier' => 'Mécanicien',  'description' => 'Réparation toutes marques. Diagnostic, vidange, freins, climatisation.',  'telephone' => '+22507200007', 'whatsapp' => null,           'adresse' => 'Garage Central',          'note' => 4.2, 'nb_avis' => 63, 'disponible' => true],
            ['nom' => "N'Guessan Paul",  'metier' => 'Peintre',     'description' => 'Peinture intérieure et extérieure. Décoration murale.',                   'telephone' => '+22507200008', 'whatsapp' => null,           'adresse' => 'Daoukro',                 'note' => 3.9, 'nb_avis' => 12, 'disponible' => false],
        ];

        foreach ($data as $item) {
            Artisan::firstOrCreate(['nom' => $item['nom'], 'metier' => $item['metier']], array_merge($item, ['is_active' => true]));
        }
    }
}
