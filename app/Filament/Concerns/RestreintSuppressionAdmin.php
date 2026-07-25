<?php

namespace App\Filament\Concerns;

/**
 * À utiliser sur les ressources de contenu (Pharmacies, Hébergements...) :
 * un modérateur peut créer/modifier, mais seul un compte admin peut
 * supprimer un enregistrement — on évite ainsi une perte de données
 * accidentelle. Un modérateur désactive (`is_active`) au lieu de supprimer.
 */
trait RestreintSuppressionAdmin
{
    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
}
