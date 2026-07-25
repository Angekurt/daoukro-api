<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Hebergement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HebergementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Hebergement::where('is_active', true)->with('ville');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $hebergements = $query->orderBy('nom')->get();

        return response()->json(['success' => true, 'data' => $hebergements]);
    }

    public function show(int $id): JsonResponse
    {
        $hebergement = Hebergement::where('is_active', true)->with('ville')->find($id);

        if (!$hebergement) {
            return response()->json(['success' => false, 'message' => 'Hébergement introuvable'], 404);
        }

        return response()->json(['success' => true, 'data' => $hebergement]);
    }}
