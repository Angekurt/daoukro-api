<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Citoyen;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    // ── Auth citoyens email (PWA daoukro-pro) ──────────────────────────────

    /**
     * Inscription citoyen par email — alternative à Google pour ceux qui
     * n'ont pas de compte Google.
     */
    public function registerCitoyen(Request $request): JsonResponse
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'prenom'    => 'nullable|string|max:100',
            'email'     => 'required|email|unique:citoyens,email',
            'telephone' => 'nullable|string|max:20',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        $citoyen = Citoyen::create([
            'name'          => $request->name,
            'prenom'        => $request->prenom,
            'email'         => $request->email,
            'telephone'     => $request->telephone,
            'password'      => Hash::make($request->password),
            'auth_provider' => 'email',
            'est_actif'     => true,
            'statut'        => 'actif',
        ]);

        $token = $citoyen->createToken('daoukro-pro')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Compte créé avec succès.',
            'token'   => $token,
            'user'    => [
                'id'         => $citoyen->id,
                'nom'        => $citoyen->name,
                'prenom'     => $citoyen->prenom,
                'email'      => $citoyen->email,
                'avatar_url' => null,
                'telephone'  => $citoyen->telephone,
            ],
        ], 201);
    }

    /**
     * Connexion citoyen par email + mot de passe (PWA daoukro-pro).
     */
    public function loginCitoyen(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $citoyen = Citoyen::where('email', $request->email)
            ->where('auth_provider', 'email')
            ->first();

        if (!$citoyen || !Hash::check($request->password, $citoyen->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect.',
            ], 401);
        }

        if (!$citoyen->est_actif || $citoyen->statut === 'suspendu') {
            return response()->json([
                'success' => false,
                'message' => 'Ce compte a été suspendu. Contactez le support.',
            ], 403);
        }

        $token = $citoyen->createToken('daoukro-pro')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie.',
            'token'   => $token,
            'user'    => [
                'id'         => $citoyen->id,
                'nom'        => $citoyen->name,
                'prenom'     => $citoyen->prenom,
                'email'      => $citoyen->email,
                'avatar_url' => $citoyen->avatar_url,
                'telephone'  => $citoyen->telephone,
            ],
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
