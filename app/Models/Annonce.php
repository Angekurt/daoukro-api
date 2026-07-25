<?php

namespace App\Models;

use App\Models\Concerns\HasPhotoGallery;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    use HasPhotoGallery;

    protected $fillable = [
        'ville_id', 'titre', 'description', 'type', 'categorie',
        'auteur', 'lieu', 'date_debut', 'date_fin',
        'contact', 'telephone', 'email', 'lien', 'photo', 'photos', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'photos' => 'array',
    ];

    protected $appends = ['photo_url', 'photos_urls'];

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }
}
