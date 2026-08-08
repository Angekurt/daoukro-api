<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Utilisateur de l'app mobile (citoyen connecté via Google). Distinct de
 * `User` (comptes admin/modérateur du panel Filament) : Citoyen n'implémente
 * pas FilamentUser, il ne peut donc structurellement pas accéder au panel
 * d'administration — pas juste une histoire de rôle.
 *
 * Étend Authenticatable (comme User) plutôt qu'un simple Model : c'est ce
 * qui permet à Sanctum de résoudre correctement $request->user() sur les
 * routes protégées par le middleware auth:sanctum.
 */
class Citoyen extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'google_id',
        'name',
        'email',
        'avatar_url',
    ];
}
