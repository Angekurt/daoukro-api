<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie que le citoyen a le droit de créer une nouvelle fiche selon son plan.
 * À appliquer sur les routes POST /mes-soumissions/*.
 *
 * Retourne 402 (Payment Required) si le quota est dépassé avec les infos
 * permettant à la PWA d'afficher la page d'abonnement.
 */
class CheckPlan
{
    public function handle(Request $request, Closure $next): Response
    {
        $citoyen = $request->user();

        if (!$citoyen) return $next($request);

        $quota  = $citoyen->quotaFiches();   // -1 = illimité
        $actuel = $citoyen->nbFichesActives();

        // -1 = illimité (plan business)
        if ($quota !== -1 && $actuel >= $quota) {
            $planActif = $citoyen->planActif();

            return response()->json([
                'success' => false,
                'code'    => 'QUOTA_DEPASSE',
                'message' => "Votre plan {$planActif} est limité à {$quota} fiche(s) active(s). "
                           . "Passez à un plan supérieur pour en ajouter davantage.",
                'data'    => [
                    'plan_actuel'    => $planActif,
                    'quota_fiches'   => $quota,
                    'fiches_actives' => $actuel,
                    'upgrade_url'    => '/abonnement',
                ],
            ], 402); // 402 Payment Required
        }

        return $next($request);
    }
}
