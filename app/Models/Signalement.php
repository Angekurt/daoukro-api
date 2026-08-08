<?php

namespace App\Models;

use App\Models\Concerns\HasPhotoGallery;
use Illuminate\Database\Eloquent\Model;

class Signalement extends Model
{
    use HasPhotoGallery;

    protected $fillable = [
        'categorie',
        'description',
        'adresse',
        'latitude',
        'longitude',
        'auteur',
        'telephone',
        'photo',
        'statut',
        'note_admin',
    ];

    protected $appends = ['photo_url'];

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }
}
