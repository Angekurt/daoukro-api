<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    protected $table = 'avis';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'nom',
        'note',
        'commentaire',
        'statut',
    ];

    protected $casts = [
        'note' => 'integer',
    ];

    public function scopeValides($query)
    {
        return $query->where('statut', 'valide');
    }

    public function scopePending($query)
    {
        return $query->where('statut', 'pending');
    }

    // Le modèle Eloquent concerné par cet avis.
    public function entite(): ?Model
    {
        return match ($this->entity_type) {
            'artisan'     => Artisan::find($this->entity_id),
            'hebergement' => Hebergement::find($this->entity_id),
            'immobilier'  => \App\Models\Immobilier::find($this->entity_id),
            'annonce'     => \App\Models\Annonce::find($this->entity_id),
            default       => null,
        };
    }

    // Recalcule note/nb_avis sur la fiche concernée à chaque changement —
    // seuls les avis validés (modérés) comptent dans la moyenne.
    protected static function booted(): void
    {
        static::saved(fn (self $avis) => $avis->recalculer());
        static::deleted(fn (self $avis) => $avis->recalculer());
    }

    protected function recalculer(): void
    {
        $entite = $this->entite();
        if (!$entite) {
            return;
        }

        $valides = static::where('entity_type', $this->entity_type)
            ->where('entity_id', $this->entity_id)
            ->valides();

        $entite->note = $valides->avg('note');
        $entite->nb_avis = $valides->count();
        $entite->saveQuietly();
    }
}
