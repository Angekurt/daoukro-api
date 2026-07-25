<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Urgence extends Model
{
    protected $fillable = [
        'nom', 'categorie', 'telephone', 'telephone2',
        'adresse', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
