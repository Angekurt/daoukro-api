<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ville extends Model
{
    protected $fillable = [
        'nom',
        'region',
        'pays',
        'latitude',
        'longitude',
        'is_active',
    ];

    // Une ville a plusieurs pharmacies
    public function pharmacies()
    {
        return $this->hasMany(Pharmacie::class);
    }

    // Une ville a plusieurs services publics
    public function servicesPublics()
    {
        return $this->hasMany(ServicePublic::class);
    }
}
