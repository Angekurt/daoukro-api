<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AppDevice extends Model
{
    protected $fillable = [
        'device_id',
        'platform',
        'device_model',
        'os_version',
        'app_version',
        'is_pwa',
        'fcm_token',
        'last_active_at',
    ];

    protected $casts = [
        'is_pwa' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    public function scopeAndroid(Builder $query): Builder
    {
        return $query->where('platform', 'android');
    }

    public function scopeIosPwa(Builder $query): Builder
    {
        return $query->where('platform', 'ios_pwa');
    }

    public function scopeWithPush(Builder $query): Builder
    {
        return $query->whereNotNull('fcm_token')->where('fcm_token', '!=', '');
    }
}
