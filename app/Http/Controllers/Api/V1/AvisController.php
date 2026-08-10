<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    private const TYPES_AUTORISES = ['artisan', 'hebergement', 'immobilier', 'annonce'];

    // Avis validés (modérés) pour une fiche donnée, plus récents en premier.
    public function index(string $type, int $id): JsonResponse
    {
        if (!in_array($type, self::TYPES_AUTORISES, true)) {
            return response()->json(['success' => false, 'message' => 'Type invalide'], 404);
        }

        $avis = Avis::where('entity_type', $type)
            ->where('entity_id', $id)
            ->valides()
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $avis]);
    }

    // Dépôt d'un avis — public (pas de compte requis), passe en modération
    // avant d'être visible (statut par défaut : pending).
    public function store(Request $request, string $type, int $id): JsonResponse
    {
        if (!in_array($type, self::TYPES_AUTORISES, true)) {
            return response()->json(['success' => false, 'message' => 'Type invalide'], 404);
        }

        $data = $request->validate([
            'nom' => 'required|string|max:100',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:500',
        ]);

        $avis = Avis::create([
            'entity_type' => $type,
            'entity_id' => $id,
            'nom' => $data['nom'],
            'note' => $data['note'],
            'commentaire' => $data['commentaire'] ?? null,
            'statut' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Merci ! Votre avis sera visible après validation.',
            'data' => $avis,
        ], 201);
    }
}
