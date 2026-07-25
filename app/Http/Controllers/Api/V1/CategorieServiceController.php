<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CategorieService;
use Illuminate\Http\JsonResponse;

class CategorieServiceController extends Controller
{
    // Liste toutes les catégories
    public function index(): JsonResponse
    {
        $categories = CategorieService::orderBy('ordre')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }
}
