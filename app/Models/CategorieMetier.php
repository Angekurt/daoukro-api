<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieMetier extends Model
{
    protected $table = 'categories_metiers';

    protected $fillable = [
        'nom',
        'description',
        'icone',
        'is_active',
    ];

    // Une catégorie a plusieurs prestataires
    public function prestataires()
    {
        return $this->hasMany(Prestataire::class);
    }
}
