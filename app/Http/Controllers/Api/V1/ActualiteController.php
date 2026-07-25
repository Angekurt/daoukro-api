<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Actualite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActualiteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Actualite::where('is_active', true);

        if ($request->has('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        $actualites = $query->orderByDesc('created_at')->get();

        // Formater created_at pour Flutter
        $actualites->each(function ($a) {
            $a->created_at_formatted = $a->created_at?->format('Y-m-d');
        });

        return response()->json(['success' => true, 'data' => $actualites]);
    }

    public function show(int $id): JsonResponse
    {
        $actualite = Actualite::where('is_active', true)->find($id);

        if (!$actualite) {
            return response()->json(['success' => false, 'message' => 'Actualité introuvable'], 404);
        }

        return response()->json(['success' => true, 'data' => $actualite]);
    }
}
