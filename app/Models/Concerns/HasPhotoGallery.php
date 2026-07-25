<?php

namespace App\Models\Concerns;

/**
 * Ajoute photo_url / photos_urls (URLs absolues) aux modèles ayant les
 * colonnes `photo` (chemin unique) et `photos` (tableau JSON de chemins).
 * Accepte aussi bien les chemins relatifs stockés par Filament que des URLs
 * déjà absolues (ex. données de démo), sans les casser.
 */
trait HasPhotoGallery
{
    protected function versUrl(string $chemin): string
    {
        return str_starts_with($chemin, 'http://') || str_starts_with($chemin, 'https://')
            ? $chemin
            : \Storage::disk('public')->url($chemin);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? $this->versUrl($this->photo) : null;
    }

    public function getPhotosUrlsAttribute(): array
    {
        return collect($this->photos ?? [])
            ->map(fn ($p) => $this->versUrl($p))
            ->all();
    }
}
