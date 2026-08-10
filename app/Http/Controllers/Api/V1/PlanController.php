<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\MoneyFusionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Gestion des plans d'abonnement depuis la PWA daoukro-pro.
 *
 * GET  /plans              → liste publique des plans avec leurs prix
 * GET  /mon-plan           → plan actuel du citoyen connecté
 * POST /plans/souscrire    → initier un paiement MoneyFusion
 * POST /paiements/webhook  → callback MoneyFusion (activer le plan après paiement)
 * GET  /paiements/verifier → vérifier manuellement le statut d'un paiement
 */
class PlanController extends Controller
{
    // ── Liste des plans ───────────────────────────────────────────────────────

    public function index(): JsonResponse
    {
        $plans = Setting::where('groupe', 'plans')
            ->orderBy('cle')
            ->get()
            ->map(fn ($s) => json_decode($s->valeur, true))
            ->filter()
            ->values();

        return response()->json(['success' => true, 'data' => $plans]);
    }

    // ── Plan actuel du citoyen ────────────────────────────────────────────────

    public function monPlan(Request $request): JsonResponse
    {
        $citoyen = $request->user();
        $planId  = $citoyen->planActif();

        $config = Setting::where('cle', "plan_{$planId}")->first();
        $details = $config ? json_decode($config->valeur, true) : [];

        return response()->json([
            'success' => true,
            'data'    => [
                'plan'           => $planId,
                'plan_expire_at' => $citoyen->plan_expire_at?->toISOString(),
                'est_actif'      => $planId !== 'free' || $citoyen->plan === 'free',
                'expire_bientot' => $citoyen->plan_expire_at
                    ? $citoyen->plan_expire_at->diffInDays(now()) <= 5
                    : false,
                'details'        => $details,
                'usage'          => [
                    'fiches_actives' => $citoyen->nbFichesActives(),
                    'quota_fiches'   => $citoyen->quotaFiches(),
                ],
            ],
        ]);
    }

    // ── Initier un paiement ───────────────────────────────────────────────────

    public function souscrire(Request $request, MoneyFusionService $mf): JsonResponse
    {
        $data = $request->validate([
            'plan_id'   => 'required|in:standard,pro,business',
            'telephone' => 'required|string|max:20',
        ]);

        // Vérifier que le plan existe et récupérer le prix
        $config = Setting::where('cle', "plan_{$data['plan_id']}")->first();
        if (!$config) {
            return response()->json(['success' => false, 'message' => 'Plan introuvable.'], 404);
        }

        $planDetails = json_decode($config->valeur, true);
        $montant     = (int) $planDetails['prix_fcfa'];

        if ($montant <= 0) {
            return response()->json(['success' => false, 'message' => 'Ce plan est gratuit, pas de paiement requis.'], 422);
        }

        $citoyen   = $request->user();
        $reference = 'DAK-' . strtoupper(Str::random(8)) . '-' . $citoyen->id;

        $resultat = $mf->initierPaiement(
            montant:     $montant,
            reference:   $reference,
            nomClient:   $citoyen->name,
            telephone:   $data['telephone'],
            description: "Abonnement Daoukro Pro — {$planDetails['nom']} (30 jours)",
        );

        if (!$resultat['success']) {
            return response()->json([
                'success' => false,
                'message' => $resultat['message'],
            ], 502);
        }

        // Sauvegarder le token en attente dans plan_details
        $citoyen->update([
            'plan_details' => array_merge($citoyen->plan_details ?? [], [
                'pending_plan'      => $data['plan_id'],
                'pending_reference' => $reference,
                'pending_token'     => $resultat['token_paiement'],
                'pending_at'        => now()->toISOString(),
            ]),
        ]);

        return response()->json([
            'success'     => true,
            'payment_url' => $resultat['payment_url'],
            'reference'   => $reference,
            'message'     => 'Redirigez vers l\'URL de paiement.',
        ]);
    }

    // ── Webhook MoneyFusion (POST depuis leur serveur) ────────────────────────

    public function webhook(Request $request): JsonResponse
    {
        // MoneyFusion envoie le statut en POST avec le token ou la référence
        $tokenPaiement = $request->input('token') ?? $request->input('payment_token');
        $statut        = strtolower($request->input('statut') ?? $request->input('status') ?? '');
        $reference     = $request->input('ref') ?? $request->input('reference');

        if (!$reference && !$tokenPaiement) {
            return response()->json(['success' => false], 400);
        }

        // Trouver le citoyen par la référence pending
        $citoyen = \App\Models\Citoyen::whereJsonContains('plan_details->pending_reference', $reference)->first()
            ?? \App\Models\Citoyen::whereJsonContains('plan_details->pending_token', $tokenPaiement)->first();

        if (!$citoyen) {
            return response()->json(['success' => false, 'message' => 'Citoyen introuvable.'], 404);
        }

        $estPaye = in_array($statut, ['paid', 'success', 'successful', 'true', '1']);

        if ($estPaye) {
            $this->activerPlan($citoyen);
        }

        return response()->json(['success' => true]);
    }

    // ── Vérification manuelle du statut (côté PWA) ────────────────────────────

    public function verifier(Request $request, MoneyFusionService $mf): JsonResponse
    {
        $citoyen = $request->user();
        $details = $citoyen->plan_details ?? [];

        $token = $details['pending_token'] ?? null;

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Aucun paiement en attente.'], 404);
        }

        $resultat = $mf->verifierStatut($token);

        if ($resultat['statut'] === 'paid') {
            $this->activerPlan($citoyen);
            return response()->json([
                'success'   => true,
                'statut'    => 'paid',
                'plan'      => $citoyen->fresh()->planActif(),
                'message'   => 'Paiement confirmé, plan activé.',
            ]);
        }

        return response()->json([
            'success' => true,
            'statut'  => $resultat['statut'],
            'message' => $resultat['message'],
        ]);
    }

    // ── Helper : activer le plan après paiement confirmé ─────────────────────

    private function activerPlan(\App\Models\Citoyen $citoyen): void
    {
        $details    = $citoyen->plan_details ?? [];
        $nouveauPlan = $details['pending_plan'] ?? null;

        if (!$nouveauPlan) return;

        $config  = Setting::where('cle', "plan_{$nouveauPlan}")->first();
        $duree   = $config ? (int)(json_decode($config->valeur, true)['duree_jours'] ?? 30) : 30;

        // Renouvellement : si plan déjà actif, on prolonge depuis la date d'expiration
        $depart = ($citoyen->plan === $nouveauPlan && $citoyen->plan_expire_at?->isFuture())
            ? $citoyen->plan_expire_at
            : now();

        $citoyen->update([
            'plan'           => $nouveauPlan,
            'plan_expire_at' => $depart->addDays($duree),
            'plan_details'   => array_merge($details, [
                'last_plan'       => $nouveauPlan,
                'last_activated'  => now()->toISOString(),
                'pending_plan'    => null,
                'pending_token'   => null,
                'pending_reference' => null,
            ]),
        ]);
    }
}
