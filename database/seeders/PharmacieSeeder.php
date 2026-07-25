<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pharmacie;
use App\Models\Ville;
use App\Models\Garde;

class PharmacieSeeder extends Seeder
{
    public function run(): void
    {
        // Récupère la ville de Daoukro
        $daoukro = Ville::where('nom', 'Daoukro')->first();

        // Pharmacie 1
        $pharmacie1 = Pharmacie::create([
            'ville_id'  => $daoukro->id,
            'nom'       => 'Pharmacie Centrale de Daoukro',
            'adresse'   => 'Avenue Principale, Daoukro',
            'telephone' => '+225 0100000001',
            'latitude'  => 7.0671,
            'longitude' => -3.9661,
            'horaires'  => 'Lun-Sam : 8h-20h | Dim : 9h-13h',
            'is_active' => true,
        ]);

        // Pharmacie 2
        $pharmacie2 = Pharmacie::create([
            'ville_id'  => $daoukro->id,
            'nom'       => 'Pharmacie du Marché',
            'adresse'   => 'Quartier Marché, Daoukro',
            'telephone' => '+225 0100000002',
            'latitude'  => 7.0680,
            'longitude' => -3.9670,
            'horaires'  => 'Lun-Sam : 7h30-21h',
            'is_active' => true,
        ]);

        // Pharmacie 3
        $pharmacie3 = Pharmacie::create([
            'ville_id'  => $daoukro->id,
            'nom'       => 'Pharmacie de la Paix',
            'adresse'   => 'Quartier Résidentiel, Daoukro',
            'telephone' => '+225 0100000003',
            'latitude'  => 7.0655,
            'longitude' => -3.9680,
            'horaires'  => 'Lun-Dim : 8h-22h',
            'is_active' => true,
        ]);

        // Garde active cette semaine → Pharmacie 1
        Garde::create([
            'pharmacie_id' => $pharmacie1->id,
            'date_debut'   => now()->startOfWeek(),
            'date_fin'     => now()->endOfWeek(),
            'note'         => 'Garde semaine en cours',
        ]);
    }
}
