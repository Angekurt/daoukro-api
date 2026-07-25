<?php

namespace App\Models;

use App\Models\Concerns\HasPhotoGallery;
use Illuminate\Database\Eloquent\Model;

class Actualite extends Model
{
    use HasPhotoGallery;

    protected $fillable = [
        'titre', 'contenu', 'photo', 'photos', 'categorie', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'photos' => 'array',
    ];

    protected $appends = ['photo_url', 'photos_urls'];
}
