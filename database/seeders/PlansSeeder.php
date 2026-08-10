<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Injecte les 4 plans tarifaires dans la table settings.
 * Chaque plan est une entrée JSON avec prix, quota_fiches, quota_membres, etc.
 * L'admin peut modifier les valeurs depuis Filament > Paramètres sans toucher au code.
 *
 * Lancer : php artisan db:seed --class=PlansSeeder
 */
class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'cle'     => 'plan_free',
                'libelle' => 'Plan Gratuit — Découverte',
                'groupe'  => 'plans',
                'valeur'  => json_encode([
                    'id'             => 'free',
                    'nom'            => 'Gratuit',
                    'description'    => 'Pour démarrer et tester la plateforme',
                    'prix_fcfa'      => 0,
                    'duree_jours'    => 0,        // 0 = illimité
                    'quota_fiches'   => 1,         // nb fiches actives max
                    'quota_photos'   => 2,         // photos par fiche
                    'quota_membres'  => 0,         // membres équipe (0 = pas d'équipe)
                    'mise_en_avant'  => false,
                    'push_mensuel'   => 0,
                    'badge_verifie'  => false,
                    'stats_avancees' => false,
                ]),
            ],
            [
                'cle'     => 'plan_standard',
                'libelle' => 'Plan Standard — Visible',
                'groupe'  => 'plans',
                'valeur'  => json_encode([
                    'id'             => 'standard',
                    'nom'            => 'Standard',
                    'description'    => 'Pour un artisan ou propriétaire indépendant',
                    'prix_fcfa'      => 2000,
                    'duree_jours'    => 30,
                    'quota_fiches'   => 3,
                    'quota_photos'   => 5,
                    'quota_membres'  => 0,
                    'mise_en_avant'  => false,
                    'push_mensuel'   => 0,
                    'badge_verifie'  => true,
                    'stats_avancees' => false,
                ]),
            ],
            [
                'cle'     => 'plan_pro',
                'libelle' => 'Plan Pro — Mis en avant',
                'groupe'  => 'plans',
                'valeur'  => json_encode([
                    'id'             => 'pro',
                    'nom'            => 'Pro',
                    'description'    => 'Pour un hôtel ou une entreprise',
                    'prix_fcfa'      => 5000,
                    'duree_jours'    => 30,
                    'quota_fiches'   => 10,
                    'quota_photos'   => 5,
                    'quota_membres'  => 3,
                    'mise_en_avant'  => true,
                    'push_mensuel'   => 1,
                    'badge_verifie'  => true,
                    'stats_avancees' => true,
                ]),
            ],
            [
                'cle'     => 'plan_business',
                'libelle' => 'Plan Business — Partenaire',
                'groupe'  => 'plans',
                'valeur'  => json_encode([
                    'id'             => 'business',
                    'nom'            => 'Business',
                    'description'    => 'Pour les établissements majeurs de Daoukro',
                    'prix_fcfa'      => 15000,
                    'duree_jours'    => 30,
                    'quota_fiches'   => -1,        // -1 = illimité
                    'quota_photos'   => 5,
                    'quota_membres'  => -1,
                    'mise_en_avant'  => true,
                    'push_mensuel'   => 4,
                    'badge_verifie'  => true,
                    'stats_avancees' => true,
                ]),
            ],
        ];

        foreach ($plans as $plan) {
            Setting::updateOrCreate(['cle' => $plan['cle']], $plan);
        }

        $this->command->info('Plans tarifaires injectés dans les paramètres.');
    }
}
