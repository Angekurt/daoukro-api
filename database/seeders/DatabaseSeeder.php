<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            VilleSeeder::class,
            CategorieServiceSeeder::class,
            PharmacieSeeder::class,
            ServicePublicSeeder::class,
            HebergementSeeder::class,
            ImmobilierSeeder::class,
            ArtisanSeeder::class,
            AnnonceSeeder::class,
            UrgenceSeeder::class,
            ActualiteSeeder::class,
        ]);
    }
}
