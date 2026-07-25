<?php

namespace App\Models;

use App\Models\Concerns\HasPhotoGallery;
use Illuminate\Database\Eloquent\Model;

class Immobilier extends Model
{
    use HasPhotoGallery;

    protected $fillable = [
        'ville_id', 'titre', 'type_offre', 'type_bien', 'description',
        'adresse', 'quartier', 'prix', 'surface', 'nb_chambres',
        'telephone', 'email', 'whatsapp', 'latitude', 'longitude',
        'photo', 'photos', 'is_active',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    protected $appends = ['photo_url', 'photos_urls'];

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }
}
