<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Services\FcmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Enregistrer ou mettre à jour un token FCM
    public function storeToken(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required|string']);

        FcmToken::firstOrCreate(['token' => $request->token]);

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

        $tokens = FcmToken::pluck('token')->toArray();

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
