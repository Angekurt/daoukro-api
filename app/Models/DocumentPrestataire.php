<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentPrestataire extends Model
{
    protected $fillable = [
        'prestataire_id',
        'type',
        'chemin_fichier',
        'nom_original',
        'taille',
        'statut',
    ];

    // Un document appartient à un prestataire
    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class);
    }
}
