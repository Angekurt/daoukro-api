<?php

namespace Database\Seeders;

use App\Models\Annonce;
use Illuminate\Database\Seeder;

class AnnonceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['titre' => 'Festival des Arts de Daoukro',     'type' => 'evenement',   'description' => 'Grand festival culturel avec expositions, concerts et spectacles de danse traditionnelle. Entrée libre.',   'auteur' => 'Mairie de Daoukro',       'lieu' => 'Place de la Mairie',    'date_debut' => '15 Fev 2025', 'date_fin' => '17 Fev 2025',  'telephone' => '+22507000001', 'email' => 'festival@daoukro.ci'],
            ['titre' => 'Recrutement Caissier(ere)',         'type' => 'emploi',      'description' => 'Supermarché Daoukro recrute un(e) caissier(ere) expérimenté(e). Salaire attractif + avantages. CV par WhatsApp.', 'auteur' => 'Supermarché Daoukro',     'lieu' => 'Daoukro Centre',        'date_debut' => "Jusqu'au 28 Fev 2025", 'date_fin' => null, 'telephone' => '+22507000002', 'email' => null],
            ['titre' => 'Restaurant Le Baobab',              'type' => 'restaurant',  'description' => 'Cuisine ivoirienne authentique. Attiéké poisson, foutou, kedjenou. Livraison disponible dans Daoukro.',      'auteur' => 'Le Baobab',               'lieu' => 'Quartier Résidentiel',  'date_debut' => null, 'date_fin' => null, 'telephone' => '+22507000003', 'email' => null],
            ['titre' => 'Soirée Dansante Club Etoile',       'type' => 'pub',         'description' => 'Grande soirée chaque vendredi soir. DJ live, ambiance garantie. Réservation de table recommandée.',          'auteur' => 'Club Etoile',             'lieu' => 'Club Etoile, Daoukro', 'date_debut' => 'Chaque vendredi', 'date_fin' => null, 'telephone' => '+22507000004', 'email' => null],
            ['titre' => 'Vente Terrain Zone Résidentielle',  'type' => 'annonce',     'description' => 'Terrain de 600m² viabilisé à vendre. Titre foncier disponible. Idéal pour construction villa.',             'auteur' => 'Agence Immobilière',      'lieu' => 'Zone Nord, Daoukro',    'date_debut' => null, 'date_fin' => null, 'telephone' => '+22507000005', 'email' => null],
            ['titre' => 'Tournoi Football Inter-Quartiers',  'type' => 'evenement',   'description' => 'Inscriptions ouvertes pour le tournoi annuel. 16 équipes. Trophée et dotations pour les 3 premiers.',        'auteur' => 'Mairie - Jeunesse',       'lieu' => 'Stade Municipal',       'date_debut' => '01 Mar 2025', 'date_fin' => null, 'telephone' => '+22507000006', 'email' => null],
            ['titre' => 'Offre Enseignant Maths',               'type' => 'emploi',      'description' => "École privée cherche enseignant(e) de mathématiques niveau lycée. CAPES ou équivalent requis.",             'auteur' => 'Groupe Scolaire Excel',   'lieu' => 'Daoukro',               'date_debut' => 'Rentrée Sept 2025', 'date_fin' => null, 'telephone' => '+22507000007', 'email' => 'rh@excellencia.ci'],
            ['titre' => 'Maquis Chez Adjoua',                'type' => 'restaurant',  'description' => 'Le meilleur garba de Daoukro ! Ouvert 7j/7 de 7h à 22h. Plats du jour à partir de 500 FCFA.',              'auteur' => 'Adjoua',                  'lieu' => 'Marché Central',        'date_debut' => null, 'date_fin' => null, 'telephone' => '+22507000008', 'email' => null],
        ];

        foreach ($data as $item) {
            Annonce::firstOrCreate(['titre' => $item['titre']], array_merge($item, ['is_active' => true]));
        }
    }
}
