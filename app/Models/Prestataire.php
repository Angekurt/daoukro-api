<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestataire extends Model
{
    protected $fillable = [
        'user_id',
        'ville_id',
        'categorie_metier_id',
        'nom_complet',
        'telephone',
        'description',
        'zone_intervention',
        'latitude',
        'longitude',
        'statut',
        'note_admin',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Un prestataire appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un prestataire appartient à une ville
    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    // Un prestataire appartient à une catégorie métier
    public function categorieMetier()
    {
        return $this->belongsTo(CategorieMetier::class);
    }

    // Un prestataire a plusieurs documents
    public function documents()
    {
        return $this->hasMany(DocumentPrestataire::class);
    }

    // Scopes statuts
    public function scopePending($query)
    {
        return $query->where('statut', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('statut', 'approved');
    }
}
