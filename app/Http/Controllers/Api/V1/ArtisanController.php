<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Artisan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtisanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Artisan::where('is_active', true)->with('ville');

        if ($request->has('metier')) {
            $query->where('metier', $request->metier);
        }

        if ($request->boolean('disponible')) {
            $query->where('disponible', true);
        }

        $artisans = $query->orderBy('nom')->get();

        return response()->json(['success' => true, 'data' => $artisans]);
    }

    public function show(int $id): JsonResponse
    {
        $artisan = Artisan::where('is_active', true)->with('ville')->find($id);

        if (!$artisan) {
            return response()->json(['success' => false, 'message' => 'Artisan introuvable'], 404);
        }

        return response()->json(['success' => true, 'data' => $artisan]);
    }

    public function metiers(): JsonResponse
    {
        $metiers = Artisan::where('is_active', true)
            ->distinct()
            ->orderBy('metier')
            ->pluck('metier');

        return response()->json(['success' => true, 'data' => $metiers]);
    }
}
