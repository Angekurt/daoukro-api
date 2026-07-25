<?php

namespace App\Models;

use App\Models\Concerns\HasPhotoGallery;
use Illuminate\Database\Eloquent\Model;

class ServicePublic extends Model
{
    use HasPhotoGallery;

    protected $table = 'services_publics'; // ← ajoute cette ligne

    protected $fillable = [
        'ville_id',
        'categorie_id',
        'nom',
        'description',
        'adresse',
        'telephone',
        'email',
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

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    public function categorie()
    {
        return $this->belongsTo(CategorieService::class, 'categorie_id');
    }
}
