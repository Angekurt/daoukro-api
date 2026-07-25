<?php

namespace Database\Seeders;

use App\Models\Urgence;
use Illuminate\Database\Seeder;

class UrgenceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nom' => 'SAMU / Urgences Médicales', 'categorie' => 'sante',    'telephone' => '185',  'telephone2' => '+22527001001', 'adresse' => 'Hôpital Général de Daoukro',      'description' => null],
            ['nom' => 'Pompiers',                   'categorie' => 'incendie', 'telephone' => '180',  'telephone2' => '+22527001002', 'adresse' => 'Caserne des Pompiers, Daoukro',    'description' => null],
            ['nom' => 'Police Nationale',           'categorie' => 'securite', 'telephone' => '111',  'telephone2' => '+22527001003', 'adresse' => 'Commissariat Central, Daoukro',    'description' => null],
            ['nom' => 'Gendarmerie Nationale',      'categorie' => 'securite', 'telephone' => '170',  'telephone2' => '+22527001004', 'adresse' => 'Brigade de Gendarmerie, Daoukro',  'description' => null],
            ['nom' => 'Hôpital Général',            'categorie' => 'sante',    'telephone' => '+22527001005', 'telephone2' => null, 'adresse' => 'Avenue de la Santé, Daoukro',      'description' => null],
            ['nom' => 'Mairie de Daoukro',          'categorie' => 'autre',    'telephone' => '+22527001006', 'telephone2' => null, 'adresse' => 'Place de la Mairie, Daoukro',      'description' => null],
            ['nom' => 'CIE (Électricité)',          'categorie' => 'autre',    'telephone' => '179',  'telephone2' => '+22527001007', 'adresse' => null,                               'description' => 'Signalement pannes électriques'],
            ['nom' => 'SODECI (Eau)',               'categorie' => 'autre',    'telephone' => '175',  'telephone2' => '+22527001008', 'adresse' => null,                               'description' => 'Signalement pannes eau'],
        ];

        foreach ($data as $item) {
            Urgence::firstOrCreate(['nom' => $item['nom']], array_merge($item, ['is_active' => true]));
        }
    }
}
