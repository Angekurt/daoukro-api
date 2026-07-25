<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategorieService;

class CategorieServiceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nom'     => 'Santé',
                'icone'   => 'hospital',
                'couleur' => '#E74C3C',
                'ordre'   => 1,
            ],
            [
                'nom'     => 'Sécurité',
                'icone'   => 'shield',
                'couleur' => '#2C3E50',
                'ordre'   => 2,
            ],
            [
                'nom'     => 'Administration',
                'icone'   => 'building',
                'couleur' => '#2980B9',
                'ordre'   => 3,
            ],
            [
                'nom'     => 'Education',
                'icone'   => 'book',
                'couleur' => '#27AE60',
                'ordre'   => 4,
            ],
            [
                'nom'     => 'Transport',
                'icone'   => 'car',
                'couleur' => '#F39C12',
                'ordre'   => 5,
            ],
        ];

        foreach ($categories as $categorie) {
            CategorieService::create($categorie);
        }
    }
}
