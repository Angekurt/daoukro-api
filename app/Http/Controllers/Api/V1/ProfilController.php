<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Profil du citoyen connecté (depuis la PWA daoukro-pro).
 * GET  /profil  → retourne les infos du citoyen
 * PUT  /profil  → met à jour nom, telephone, bio
 */
class ProfilController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $citoyen = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'         => $citoyen->id,
                'nom'        => $citoyen->name,
                'email'      => $citoyen->email,
                'avatar_url' => $citoyen->avatar_url,
                'telephone'  => $citoyen->telephone ?? null,
                'bio'        => $citoyen->bio ?? null,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nom'       => 'sometimes|required|string|max:100',
            'telephone' => 'nullable|string|max:30',
            'bio'       => 'nullable|string|max:500',
        ]);

        $citoyen = $request->user();

        if (isset($data['nom'])) {
            $citoyen->name = $data['nom'];
        }
        if (array_key_exists('telephone', $data)) {
            $citoyen->telephone = $data['telephone'];
        }
        if (array_key_exists('bio', $data)) {
            $citoyen->bio = $data['bio'];
        }

        $citoyen->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour.',
            'data'    => [
                'id'         => $citoyen->id,
                'nom'        => $citoyen->name,
                'email'      => $citoyen->email,
                'avatar_url' => $citoyen->avatar_url,
                'telephone'  => $citoyen->telephone,
                'bio'        => $citoyen->bio,
            ],
        ]);
    }
}
