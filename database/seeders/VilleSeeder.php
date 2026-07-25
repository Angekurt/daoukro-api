<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ville;

class VilleSeeder extends Seeder
{
    public function run(): void
    {
        Ville::create([
            'nom'       => 'Daoukro',
            'region'    => 'Iffou',
            'pays'      => "Côte d'Ivoire",
            'latitude'  => 7.0667,
            'longitude' => -3.9667,
            'is_active' => true,
        ]);
    }
}
