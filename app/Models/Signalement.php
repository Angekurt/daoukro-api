<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signalement extends Model
{
    protected $fillable = [
        'categorie',
        'description',
        'adresse',
        'latitude',
        'longitude',
        'auteur',
        'telephone',
        'statut',
        'note_admin',
    ];

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }
}
