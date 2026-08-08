<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Citoyen;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Inscription
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'role'     => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Compte créé avec succès',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    // Connexion
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    // Connexion citoyenne via Google (app mobile) — crée le compte au premier
    // passage, le retrouve ensuite. Sépare volontairement les comptes
    // citoyens (table `citoyens`) des comptes admin/modérateur (`users`).
    public function google(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        // Vérifie le jeton auprès de Google (signature + expiration) — pas
        // de dépendance supplémentaire nécessaire pour ça.
        $reponse = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->id_token,
        ]);

        if (!$reponse->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Jeton Google invalide ou expiré.',
            ], 401);
        }

        $payload = $reponse->json();

        $clientId = config('services.google.client_id');
        if (!$clientId || ($payload['aud'] ?? null) !== $clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Jeton Google non reconnu pour cette application.',
            ], 401);
        }

        $citoyen = Citoyen::where('google_id', $payload['sub'])->first();

        if ($citoyen) {
            $citoyen->update([
                'name' => $payload['name'] ?? $citoyen->name,
                'avatar_url' => $payload['picture'] ?? $citoyen->avatar_url,
            ]);
        } else {
            $citoyen = Citoyen::create([
                'google_id' => $payload['sub'],
                'name' => $payload['name'] ?? ($payload['email'] ?? 'Citoyen'),
                'email' => $payload['email'] ?? null,
                'avatar_url' => $payload['picture'] ?? null,
            ]);
        }

        $token = $citoyen->createToken('mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $citoyen->id,
                'nom' => $citoyen->name,
                'email' => $citoyen->email,
                'avatar_url' => $citoyen->avatar_url,
            ],
        ]);
    }

    // Déconnexion
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }

    // Profil connecté
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $request->user(),
        ]);
    }
}
