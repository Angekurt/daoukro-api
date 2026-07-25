<?php

namespace App\Models;

use App\Models\Concerns\HasPhotoGallery;
use Illuminate\Database\Eloquent\Model;

class Pharmacie extends Model
{
    use HasPhotoGallery;

    protected $fillable = [
        'ville_id',
        'nom',
        'adresse',
        'telephone',
        'latitude',
        'longitude',
        'horaires',
        'photo',
        'photos',
        'is_active',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    protected $appends = ['photo_url', 'photos_urls'];

    // Une pharmacie appartient à une ville
    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    // Une pharmacie a plusieurs gardes
    public function gardes()
    {
        return $this->hasMany(Garde::class);
    }

    // Gardes actives aujourd'hui
    public function gardesActives()
    {
        return $this->hasMany(Garde::class)
            ->whereDate('date_debut', '<=', now())
            ->whereDate('date_fin', '>=', now());
    }
}
