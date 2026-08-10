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
        'google_id', 'name', 'email', 'avatar_url',
        'telephone', 'bio', 'plan', 'plan_expire_at', 'plan_details',
    ];

    protected $casts = [
        'plan_expire_at' => 'datetime',
        'plan_details'   => 'array',
    ];

    /** Retourne le plan effectif en tenant compte de l'expiration */
    public function planActif(): string
    {
        if ($this->plan === 'free') return 'free';
        if ($this->plan_expire_at && $this->plan_expire_at->isPast()) return 'free';
        return $this->plan;
    }

    /** Quota de fiches actives selon le plan (-1 = illimité) */
    public function quotaFiches(): int
    {
        $config = \App\Models\Setting::where('cle', "plan_{$this->planActif()}")->first();
        if (!$config) return 1;
        $data = json_decode($config->valeur, true);
        return (int) ($data['quota_fiches'] ?? 1);
    }

    /** Nombre de fiches actives toutes catégories confondues */
    public function nbFichesActives(): int
    {
        return $this->artisans()->where('is_active', true)->count()
             + $this->hebergements()->where('is_active', true)->count()
             + $this->immobiliers()->where('is_active', true)->count()
             + $this->annonces()->where('is_active', true)->count();
    }

    public function artisans()
    {
        return $this->hasMany(Artisan::class);
    }

    public function hebergements()
    {
        return $this->hasMany(Hebergement::class);
    }

    public function immobiliers()
    {
        return $this->hasMany(Immobilier::class);
    }

    public function annonces()
    {
        return $this->hasMany(Annonce::class);
    }

    /** Équipes dont ce citoyen est propriétaire */
    public function teams()
    {
        return $this->hasMany(\App\Models\Team::class, 'owner_id');
    }

    /** Équipes dont ce citoyen est membre invité */
    public function teamsMembre()
    {
        return $this->belongsToMany(\App\Models\Team::class, 'team_members', 'citoyen_id', 'team_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }
}
