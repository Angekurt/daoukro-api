<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SignalementController extends Controller
{
    // Dépôt d'un signalement — public, pas de compte requis.
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'categorie' => 'required|in:voirie,eclairage,dechets,eau,securite,autre',
            'description' => 'required|string|min:10|max:1000',
            'adresse' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'auteur' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:30',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('signalements', 'public');
        }

        $signalement = Signalement::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Signalement transmis à la mairie.',
            'data' => $signalement,
        ], 201);
    }
}
