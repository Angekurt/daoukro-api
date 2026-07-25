<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ServicePublic;
use Illuminate\Http\JsonResponse;

class ServicePublicController extends Controller
{
    // Liste tous les services publics actifs
    public function index(): JsonResponse
    {
        $services = ServicePublic::where('is_active', true)
            ->with(['ville', 'categorie'])
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $services,
        ]);
    }

    // Détail d'un service public
    public function show(int $id): JsonResponse
    {
        $service = ServicePublic::where('is_active', true)
            ->with(['ville', 'categorie'])
            ->find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service introuvable',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $service,
        ]);
    }

    // Services filtrés par catégorie
    public function parCategorie(int $id): JsonResponse
    {
        $services = ServicePublic::where('is_active', true)
            ->where('categorie_id', $id)
            ->with(['ville', 'categorie'])
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $services,
        ]);
    }
}
