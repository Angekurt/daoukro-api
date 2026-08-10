<?php

namespace App\Models;

use App\Models\Concerns\HasPhotoGallery;
use Illuminate\Database\Eloquent\Model;

class Artisan extends Model
{
    use HasPhotoGallery;

    protected $fillable = [
        'citoyen_id', 'ville_id', 'nom', 'metier', 'description',
        'telephone', 'whatsapp', 'email', 'adresse',
        'latitude', 'longitude', 'photo', 'photos',
        'note', 'nb_avis', 'disponible', 'is_active', 'motif_rejet',
    ];

    protected $casts = [
        'photos'     => 'array',
        'disponible' => 'boolean',
        'is_active'  => 'boolean',
        'note'       => 'float',
        'latitude'   => 'float',
        'longitude'  => 'float',
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
