<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['cle' => 'support_whatsapp', 'valeur' => '2250798240515', 'groupe' => 'support', 'libelle' => 'Numéro WhatsApp du support'],
            ['cle' => 'support_email', 'valeur' => 'contact@akdev.ci', 'groupe' => 'support', 'libelle' => 'E-mail du support'],
            ['cle' => 'a_propos_texte', 'valeur' => "Daoukro Digital est l'application officielle de la ville de Daoukro, en Côte d'Ivoire. Elle centralise les services essentiels : pharmacies de garde, services publics, artisans locaux, hébergements, annonces, urgences et actualités de la ville.", 'groupe' => 'contenu', 'libelle' => 'Texte de la page À propos'],
            ['cle' => 'message_accueil', 'valeur' => '', 'groupe' => 'contenu', 'libelle' => "Bandeau optionnel en haut de l'accueil"],
            ['cle' => 'urgences_numeros', 'valeur' => json_encode(['police' => '111', 'pompiers' => '180', 'samu' => '185', 'gendarmerie' => '170']), 'groupe' => 'urgences', 'libelle' => "Numéros d'urgence globaux"],
            ['cle' => 'app_version_min', 'valeur' => '1.0.0', 'groupe' => 'technique', 'libelle' => 'Version minimale requise de l\'application'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['cle' => $setting['cle']], $setting);
        }
    }
}
