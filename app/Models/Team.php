<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['owner_id', 'nom', 'description', 'logo'];

    /** Propriétaire de l'équipe */
    public function owner()
    {
        return $this->belongsTo(Citoyen::class, 'owner_id');
    }

    /** Membres (pivot team_members) */
    public function membres()
    {
        return $this->belongsToMany(Citoyen::class, 'team_members', 'team_id', 'citoyen_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /** Invitations en attente */
    public function invitations()
    {
        return $this->hasMany(TeamInvitation::class);
    }

    /** Vérifie si un citoyen est membre ou propriétaire */
    public function aMembre(int $citoyenId): bool
    {
        if ($this->owner_id === $citoyenId) return true;
        return $this->membres()->where('citoyen_id', $citoyenId)->exists();
    }

    /** Vérifie si un citoyen peut gérer (créer/modifier) les fiches */
    public function peutGerer(int $citoyenId): bool
    {
        if ($this->owner_id === $citoyenId) return true;
        return $this->membres()
                    ->where('citoyen_id', $citoyenId)
                    ->wherePivot('role', 'manager')
                    ->exists();
    }
}
