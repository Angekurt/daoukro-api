<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Envoi de notifications push via l'API FCM HTTP v1 (OAuth2 par compte de
// service). L'ancienne API legacy (fcm.googleapis.com/fcm/send + clé serveur)
// est fermée par Google depuis juin 2024, elle ne fonctionne plus sur les
// projets Firebase récents.
class FcmService
{
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    // Envoie la même notification à une liste de tokens d'appareils.
    // Retourne le nombre d'envois réussis.
    public function envoyerA(array $tokens, string $titre, string $corps, array $data = []): int
    {
        $projetId = config('services.fcm.project_id');
        $accessToken = $this->obtenirAccessToken();

        if (! $accessToken || ! $projetId) {
            Log::warning('FCM non configuré : compte de service ou project_id manquant.');
            return 0;
        }

        $envoyes = 0;
        $tokensInvalides = [];

        foreach ($tokens as $token) {
            $reponse = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projetId}/messages:send", [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $titre,
                            'body' => $corps,
                        ],
                        'data' => array_map('strval', $data),
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'sound' => 'default',
                                'channel_id' => 'daoukro_channel',
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            ],
                        ],
                        'webpush' => [
                            'headers' => [
                                'Urgency' => 'high',
                            ],
                            'notification' => [
                                'title' => $titre,
                                'body' => $corps,
                                'icon' => '/icons/Icon-192.png',
                                'badge' => '/icons/Icon-192.png',
                            ],
                            'fcm_options' => [
                                'link' => 'https://daoukro.akdev.ci/pwa/',
                            ],
                        ],
                        'apns' => [
                            'headers' => [
                                'apns-priority' => '10',
                            ],
                            'payload' => [
                                'aps' => [
                                    'alert' => [
                                        'title' => $titre,
                                        'body' => $corps,
                                    ],
                                    'sound' => 'default',
                                    'badge' => 1,
                                ],
                            ],
                        ],
                    ],
                ]);

            if ($reponse->successful()) {
                $envoyes++;
            } else {
                $status = $reponse->status();
                $body = $reponse->json() ?? [];
                $errorCode = $body['error']['details'][0]['errorCode'] ?? $body['error']['status'] ?? null;

                // Si le token est désenregistré ou invalide (ex: appli désinstallée)
                if ($status === 404 || in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT', 'NOT_FOUND'])) {
                    $tokensInvalides[] = $token;
                }

                Log::warning('Échec envoi FCM', [
                    'token_debut' => substr($token, 0, 12) . '…',
                    'reponse' => $body ?: $reponse->body(),
                ]);
            }
        }

        // Nettoyage des tokens invalides pour garder une base saine
        if (! empty($tokensInvalides)) {
            \App\Models\AppDevice::whereIn('fcm_token', $tokensInvalides)->update(['fcm_token' => null]);
            \App\Models\FcmToken::whereIn('token', $tokensInvalides)->delete();
        }

        return $envoyes;
    }

    public function estConfigure(): bool
    {
        $chemin = config('services.fcm.credentials_path');
        return (bool) config('services.fcm.project_id') && $chemin && file_exists($chemin);
    }

    private function obtenirAccessToken(): ?string
    {
        return Cache::remember('fcm_access_token', 3000, function () {
            $chemin = config('services.fcm.credentials_path');

            if (! $chemin || ! file_exists($chemin)) {
                Log::warning("FCM : fichier de compte de service introuvable ($chemin).");
                return null;
            }

            $credentials = json_decode(file_get_contents($chemin), true);

            if (! isset($credentials['client_email'], $credentials['private_key'])) {
                Log::warning('FCM : fichier de compte de service invalide.');
                return null;
            }

            $maintenant = time();
            $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claims = $this->base64UrlEncode(json_encode([
                'iss' => $credentials['client_email'],
                'scope' => self::SCOPE,
                'aud' => self::TOKEN_URL,
                'exp' => $maintenant + 3600,
                'iat' => $maintenant,
            ]));

            $donneesSignature = "{$header}.{$claims}";
            openssl_sign($donneesSignature, $signature, $credentials['private_key'], 'sha256WithRSAEncryption');
            $jwt = "{$donneesSignature}." . $this->base64UrlEncode($signature);

            $reponse = Http::asForm()->post(self::TOKEN_URL, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if (! $reponse->successful()) {
                Log::warning('FCM : impossible d\'obtenir un token OAuth2.', ['reponse' => $reponse->body()]);
                return null;
            }

            return $reponse->json('access_token');
        });
    }

    private function base64UrlEncode(string $donnees): string
    {
        return rtrim(strtr(base64_encode($donnees), '+/', '-_'), '=');
    }
}
