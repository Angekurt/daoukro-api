<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    // Toutes les valeurs de configuration pilotables depuis l'admin,
    // sous forme de map clé → valeur (consommée au démarrage de l'app).
    public function index(): JsonResponse
    {
        $data = Setting::pluck('valeur', 'cle');

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
