<?php

namespace App\Observers;

use App\Mail\FicheRejetee;
use App\Mail\FicheValidee;
use App\Services\FcmService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Observer générique pour les modèles Artisan, Hebergement, Immobilier, Annonce.
 *
 * Déclenche automatiquement :
 * - Email au citoyen propriétaire quand sa fiche passe à is_active = true
 *   (validation) ou quand un admin la marque explicitement refusée.
 * - Notification push FCM à tous les appareils quand une nouvelle fiche
 *   est publiée (annonce, actualité).
 */
class FicheObserver
{
    // Labels humains par classe de modèle
    private const LABELS = [
        'Artisan'      => 'Artisan',
        'Hebergement'  => 'Hébergement',
        'Immobilier'   => 'Bien immobilier',
        'Annonce'      => 'Annonce',
        'Actualite'    => 'Actualité',
    ];

    // Types de fiche qui déclenchent une push publique lors de la publication
    private const TYPES_PUSH_PUBLIQUE = ['Annonce', 'Actualite'];

    public function updated(Model $fiche): void
    {
        $classe = class_basename($fiche);

        // ── Email au professionnel ──────────────────────────────────────────
        if ($fiche->isDirty('is_active') && $fiche->citoyen_id) {
            $citoyen  = $fiche->citoyen;
            $email    = $citoyen?->email;
            $nomDest  = $citoyen?->name ?? 'Professionnel';
            $nomFiche = $fiche->nom ?? $fiche->titre ?? '—';
            $label    = self::LABELS[$classe] ?? $classe;

            if ($email) {
                try {
                    if ($fiche->is_active) {
                        Mail::to($email)->send(new FicheValidee($nomDest, $nomFiche, $label));
                    } elseif ($fiche->isDirty('motif_rejet') || isset($fiche->motif_rejet)) {
                        // On envoie le rejet seulement si un motif est renseigné
                        // pour éviter les faux positifs (désactivation admin classique)
                        Mail::to($email)->send(new FicheRejetee($nomDest, $nomFiche, $label, $fiche->motif_rejet));
                    }
                } catch (\Throwable $e) {
                    Log::warning("FicheObserver: échec envoi email [{$classe}#{$fiche->id}]", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // ── Push publique (annonces/actualités nouvellement publiées) ───────
        if ($fiche->isDirty('is_active') && $fiche->is_active && in_array($classe, self::TYPES_PUSH_PUBLIQUE)) {
            $this->envoyerPushPublication($fiche, $classe);
        }
    }

    private function envoyerPushPublication(Model $fiche, string $classe): void
    {
        try {
            $fcm    = app(FcmService::class);
            if (! $fcm->estConfigure()) return;

            $tokens = \App\Models\FcmToken::pluck('token')->toArray();
            if (empty($tokens)) return;

            if ($classe === 'Annonce') {
                $type       = $fiche->type ?? 'annonce';
                $titreNotif = match($type) {
                    'evenement'  => 'Nouvel événement à Daoukro',
                    'emploi'     => 'Offre d\'emploi disponible',
                    'restaurant' => 'Nouveau restaurant / maquis',
                    default      => 'Nouvelle annonce',
                };
                $corps = $fiche->titre;
            } else {
                // Actualité
                $titreNotif = 'Nouvelle actualité';
                $corps      = $fiche->titre;
            }

            $fcm->envoyerA($tokens, $titreNotif, $corps, [
                'type'     => $classe === 'Annonce' ? ($fiche->type ?? 'annonce') : 'actualite',
                'fiche_id' => (string) $fiche->id,
                'titre'    => $titreNotif,
                'corps'    => $corps,
            ]);
        } catch (\Throwable $e) {
            Log::warning("FicheObserver: échec push publication [{$classe}#{$fiche->id}]", [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
