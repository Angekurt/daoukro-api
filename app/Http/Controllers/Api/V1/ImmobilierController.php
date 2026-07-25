<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Immobilier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImmobilierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Immobilier::where('is_active', true)->with('ville');

        if ($request->has('type_offre')) {
            $query->where('type_offre', $request->type_offre);
        }

        if ($request->has('type_bien')) {
            $query->where('type_bien', $request->type_bien);
        }

        $biens = $query->orderByDesc('created_at')->get();

        return response()->json(['success' => true, 'data' => $biens]);
    }

    public function show(int $id): JsonResponse
    {
        $bien = Immobilier::where('is_active', true)->with('ville')->find($id);

        if (!$bien) {
            return response()->json(['success' => false, 'message' => 'Bien introuvable'], 404);
        }

        return response()->json(['success' => true, 'data' => $bien]);
    }
}
