<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnonceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Annonce::where('is_active', true);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $annonces = $query->orderByDesc('created_at')->get();

        return response()->json(['success' => true, 'data' => $annonces]);
    }

    public function show(int $id): JsonResponse
    {
        $annonce = Annonce::where('is_active', true)->find($id);

        if (!$annonce) {
            return response()->json(['success' => false, 'message' => 'Annonce introuvable'], 404);
        }

        return response()->json(['success' => true, 'data' => $annonce]);
    }

    // ── Intérêt emploi ────────────────────────────────────────────────────────

    /** Marquer son intérêt pour une offre d'emploi (citoyen connecté) */
    public function marquerInteret(Request $request, int $id): JsonResponse
    {
        $annonce = Annonce::where('is_active', true)->where('type', 'emploi')->find($id);

        if (!$annonce) {
            return response()->json(['success' => false, 'message' => 'Offre introuvable.'], 404);
        }

        $citoyen = $request->user();

        // Upsert — idempotent
        DB::table('interets_emploi')->insertOrIgnore([
            'annonce_id' => $annonce->id,
            'citoyen_id' => $citoyen->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $total = DB::table('interets_emploi')->where('annonce_id', $annonce->id)->count();

        return response()->json([
            'success'         => true,
            'message'         => 'Votre intérêt a été enregistré.',
            'total_interets'  => $total,
            'deja_interesse'  => true,
        ]);
    }

    /** Retirer son intérêt */
    public function retirerInteret(Request $request, int $id): JsonResponse
    {
        $citoyen = $request->user();

        DB::table('interets_emploi')
            ->where('annonce_id', $id)
            ->where('citoyen_id', $citoyen->id)
            ->delete();

        $total = DB::table('interets_emploi')->where('annonce_id', $id)->count();

        return response()->json([
            'success'        => true,
            'message'        => 'Intérêt retiré.',
            'total_interets' => $total,
            'deja_interesse' => false,
        ]);
    }

    /** Stats intérêts pour une annonce (lecture publique) */
    public function statsInterets(Request $request, int $id): JsonResponse
    {
        $total = DB::table('interets_emploi')->where('annonce_id', $id)->count();

        $dejaInteresse = false;
        if ($request->bearerToken()) {
            try {
                $citoyen = $request->user();
                if ($citoyen) {
                    $dejaInteresse = DB::table('interets_emploi')
                        ->where('annonce_id', $id)
                        ->where('citoyen_id', $citoyen->id)
                        ->exists();
                }
            } catch (\Throwable) {}
        }

        return response()->json([
            'success'        => true,
            'total_interets' => $total,
            'deja_interesse' => $dejaInteresse,
        ]);
    }
}
