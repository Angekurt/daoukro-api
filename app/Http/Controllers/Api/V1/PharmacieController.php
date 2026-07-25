<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pharmacie;
use App\Models\Garde;
use Illuminate\Http\JsonResponse;

class PharmacieController extends Controller
{
    // Liste toutes les pharmacies actives
    public function index(): JsonResponse
    {
        $pharmacies = Pharmacie::where('is_active', true)
            ->with('ville')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $pharmacies,
        ]);
    }

    // Détail d'une pharmacie
    public function show(int $id): JsonResponse
    {
        $pharmacie = Pharmacie::where('is_active', true)
            ->with('ville')
            ->find($id);

        if (!$pharmacie) {
            return response()->json([
                'success' => false,
                'message' => 'Pharmacie introuvable',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $pharmacie,
        ]);
    }

    // Pharmacies de garde aujourd'hui
    public function gardesActives(): JsonResponse
    {
        $gardes = Garde::actives()
            ->with(['pharmacie', 'pharmacie.ville'])
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $gardes,
        ]);
    }
}
