<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieService extends Model
{
    protected $table = 'categories_services'; // ← ajoute cette ligne

    protected $fillable = [
        'nom',
        'icone',
        'couleur',
        'ordre',
    ];

    public function servicesPublics()
    {
        return $this->hasMany(ServicePublic::class, 'categorie_id');
    }
}
