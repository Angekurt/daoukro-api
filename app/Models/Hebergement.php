<?php

namespace App\Models;

use App\Models\Concerns\HasPhotoGallery;
use Illuminate\Database\Eloquent\Model;

class Hebergement extends Model
{
    use HasPhotoGallery;

    protected $fillable = [
        'citoyen_id', 'ville_id', 'nom', 'type', 'description', 'adresse',
        'telephone', 'email', 'whatsapp', 'latitude', 'longitude',
        'prix_min', 'prix_max', 'photo', 'photos', 'note', 'nb_avis', 'is_active', 'motif_rejet',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    protected $appends = ['photo_url', 'photos_urls'];

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    public function citoyen()
    {
        return $this->belongsTo(Citoyen::class);
    }
}
