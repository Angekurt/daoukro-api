<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Urgence;
use Illuminate\Http\JsonResponse;

class UrgenceController extends Controller
{
    public function index(): JsonResponse
    {
        $urgences = Urgence::where('is_active', true)
            ->orderBy('categorie')
            ->orderBy('nom')
            ->get();

        return response()->json(['success' => true, 'data' => $urgences]);
    }

    public function show(int $id): JsonResponse
    {
        $urgence = Urgence::where('is_active', true)->find($id);

        if (!$urgence) {
            return response()->json(['success' => false, 'message' => 'Urgence introuvable'], 404);
        }

        return response()->json(['success' => true, 'data' => $urgence]);
    }
}
