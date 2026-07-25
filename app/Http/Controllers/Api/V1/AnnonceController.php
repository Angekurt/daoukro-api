<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
