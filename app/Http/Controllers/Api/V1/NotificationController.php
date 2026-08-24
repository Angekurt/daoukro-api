<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AppDevice;
use App\Models\FcmToken;
use App\Services\FcmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Enregistre ou met à jour un appareil (matériel unique ou installation PWA)
    // Permet d'éviter les doublons lors des réinstallations APK et de tracker les PWA iOS.
    public function registerDevice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id'    => 'required|string|max:191',
            'platform'     => 'nullable|string|in:android,ios_pwa,ios_app,web',
            'device_model' => 'nullable|string|max:100',
            'os_version'   => 'nullable|string|max:50',
            'app_version'  => 'nullable|string|max:50',
            'is_pwa'       => 'nullable|boolean',
            'fcm_token'    => 'nullable|string|max:255',
        ]);

        $attributes = [
            'platform'       => $validated['platform'] ?? 'android',
            'device_model'   => $validated['device_model'] ?? null,
            'os_version'     => $validated['os_version'] ?? null,
            'app_version'    => $validated['app_version'] ?? null,
            'is_pwa'         => $validated['is_pwa'] ?? false,
            'last_active_at' => now(),
        ];

        // Ne mettre à jour le token FCM que s'il est explicitement fourni
        // afin de ne pas écraser un token valide lors du ping initial de l'application
        if (! empty($validated['fcm_token'])) {
            $attributes['fcm_token'] = $validated['fcm_token'];
        }

        $device = AppDevice::updateOrCreate(
            ['device_id' => $validated['device_id']],
            $attributes
        );

        // Rétrocompatibilité : synchroniser également la table fcm_tokens si un token est présent
        if (! empty($validated['fcm_token'])) {
            FcmToken::firstOrCreate(['token' => $validated['fcm_token']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Appareil enregistré avec succès',
            'device'  => $device,
        ]);
    }

    // Enregistrer ou mettre à jour un token FCM (rétrocompatibilité)
    public function storeToken(Request $request): JsonResponse
    {
        $request->validate([
            'token'     => 'required|string',
            'device_id' => 'nullable|string',
            'platform'  => 'nullable|string',
        ]);

        FcmToken::firstOrCreate(['token' => $request->token]);

        if ($request->filled('device_id')) {
            AppDevice::updateOrCreate(
                ['device_id' => $request->device_id],
                [
                    'fcm_token'      => $request->token,
                    'platform'       => $request->platform ?? 'android',
                    'last_active_at' => now(),
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    // Envoyer une notification à tous les appareils enregistrés
    // Appelé depuis le panel admin Filament ou manuellement
    public function envoyer(Request $request, FcmService $fcm): JsonResponse
    {
        $request->validate([
            'titre' => 'required|string|max:100',
            'corps' => 'required|string|max:500',
            'type'  => 'nullable|string', // alerte, sante, mairie, pharmacie
        ]);

        // Récupérer tous les tokens uniques valides
        $deviceTokens = AppDevice::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->pluck('fcm_token')
            ->toArray();

        $legacyTokens = FcmToken::pluck('token')->toArray();

        $tokens = array_values(array_unique(array_filter(array_merge($deviceTokens, $legacyTokens))));

        if (empty($tokens)) {
            return response()->json(['success' => false, 'message' => 'Aucun appareil enregistré'], 404);
        }

        if (! $fcm->estConfigure()) {
            return response()->json(['success' => false, 'message' => 'FCM non configuré côté serveur'], 500);
        }

        $envoyes = $fcm->envoyerA($tokens, $request->titre, $request->corps, [
            'type'  => $request->type ?? 'info',
            'titre' => $request->titre,
            'corps' => $request->corps,
        ]);

        return response()->json([
            'success'  => true,
            'envoye_a' => "$envoyes / " . count($tokens) . ' appareils',
        ]);
    }
}
