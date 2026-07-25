<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garde extends Model
{
    protected $fillable = [
        'pharmacie_id',
        'date_debut',
        'date_fin',
        'note',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    // Une garde appartient à une pharmacie
    public function pharmacie()
    {
        return $this->belongsTo(Pharmacie::class);
    }

    // Scope : gardes actives aujourd'hui
    public function scopeActives($query)
    {
        return $query
            ->whereDate('date_debut', '<=', now())
            ->whereDate('date_fin', '>=', now());
    }
}
