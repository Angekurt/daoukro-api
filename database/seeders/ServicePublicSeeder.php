<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServicePublic;
use App\Models\Ville;
use App\Models\CategorieService;

class ServicePublicSeeder extends Seeder
{
    public function run(): void
    {
        $daoukro   = Ville::where('nom', 'Daoukro')->first();
        $sante     = CategorieService::where('nom', 'Santé')->first();
        $securite  = CategorieService::where('nom', 'Sécurité')->first();
        $admin     = CategorieService::where('nom', 'Administration')->first();

        $services = [
            [
                'ville_id'     => $daoukro->id,
                'categorie_id' => $sante->id,
                'nom'          => 'Hôpital Général de Daoukro',
                'adresse'      => 'Route de l\'Hôpital, Daoukro',
                'telephone'    => '+225 0100000010',
                'latitude'     => 7.0660,
                'longitude'    => -3.9650,
                'horaires'     => '24h/24 - 7j/7',
                'is_active'    => true,
            ],
            [
                'ville_id'     => $daoukro->id,
                'categorie_id' => $sante->id,
                'nom'          => 'Centre de Santé Urbain',
                'adresse'      => 'Quartier Centre, Daoukro',
                'telephone'    => '+225 0100000011',
                'latitude'     => 7.0675,
                'longitude'    => -3.9655,
                'horaires'     => 'Lun-Ven : 7h30-17h30',
                'is_active'    => true,
            ],
            [
                'ville_id'     => $daoukro->id,
                'categorie_id' => $securite->id,
                'nom'          => 'Commissariat de Police de Daoukro',
                'adresse'      => 'Avenue de la République, Daoukro',
                'telephone'    => '+225 0100000020',
                'latitude'     => 7.0665,
                'longitude'    => -3.9672,
                'horaires'     => '24h/24 - 7j/7',
                'is_active'    => true,
            ],
            [
                'ville_id'     => $daoukro->id,
                'categorie_id' => $securite->id,
                'nom'          => 'Gendarmerie Nationale de Daoukro',
                'adresse'      => 'Route Nationale, Daoukro',
                'telephone'    => '+225 0100000021',
                'latitude'     => 7.0690,
                'longitude'    => -3.9660,
                'horaires'     => '24h/24 - 7j/7',
                'is_active'    => true,
            ],
            [
                'ville_id'     => $daoukro->id,
                'categorie_id' => $admin->id,
                'nom'          => 'Mairie de Daoukro',
                'adresse'      => 'Place de la Mairie, Daoukro',
                'telephone'    => '+225 0100000030',
                'latitude'     => 7.0668,
                'longitude'    => -3.9665,
                'horaires'     => 'Lun-Ven : 8h-17h',
                'is_active'    => true,
            ],
            [
                'ville_id'     => $daoukro->id,
                'categorie_id' => $admin->id,
                'nom'          => 'Sous-Préfecture de Daoukro',
                'adresse'      => 'Avenue Administrative, Daoukro',
                'telephone'    => '+225 0100000031',
                'latitude'     => 7.0672,
                'longitude'    => -3.9658,
                'horaires'     => 'Lun-Ven : 8h-17h',
                'is_active'    => true,
            ],
        ];

        foreach ($services as $service) {
            ServicePublic::create($service);
        }
    }
}
