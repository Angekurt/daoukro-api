<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service d'intégration MoneyFusion — API de paiement mobile money CI.
 *
 * Endpoints utilisés :
 *   POST /initiatePayment  → créer une session de paiement, retourne l'URL de paiement
 *   POST /checkStatus      → vérifier le statut d'un paiement
 *
 * Documentation : https://moneyfusion.net/documentation
 *
 * Variables .env requises :
 *   MONEYFUSION_TOKEN=votre_token_api
 *   MONEYFUSION_BASE_URL=https://www.pay.moneyfusion.net
 *   MONEYFUSION_RETURN_URL=https://pro.daoukro-digital.akdev.tech/paiement/retour
 */
class MoneyFusionService
{
    private string $baseUrl;
    private string $token;
    private string $returnUrl;

    public function __construct()
    {
        $this->baseUrl   = rtrim(config('services.moneyfusion.base_url', 'https://www.pay.moneyfusion.net'), '/');
        $this->token     = config('services.moneyfusion.token', '');
        $this->returnUrl = config('services.moneyfusion.return_url', '');
    }

    public function estConfigure(): bool
    {
        return !empty($this->token) && !empty($this->returnUrl);
    }

    /**
     * Initie un paiement MoneyFusion.
     *
     * @param float  $montant      Montant en FCFA
     * @param string $reference    Référence unique côté notre système
     * @param string $nomClient    Nom du payeur
     * @param string $telephone    Numéro de téléphone (Orange Money, Wave, MTN...)
     * @param string $description  Description affichée sur la page de paiement
     *
     * @return array{success: bool, payment_url: ?string, token_paiement: ?string, message: string}
     */
    public function initierPaiement(
        float  $montant,
        string $reference,
        string $nomClient,
        string $telephone,
        string $description,
    ): array {
        if (!$this->estConfigure()) {
            return ['success' => false, 'payment_url' => null, 'token_paiement' => null, 'message' => 'MoneyFusion non configuré.'];
        }

        try {
            $reponse = Http::withToken($this->token)
                ->post("{$this->baseUrl}/initiatePayment", [
                    'totalPrice'    => (int) $montant,
                    'articleName'   => $description,
                    'ref'           => $reference,
                    'customer_name' => $nomClient,
                    'customer_phone'=> $telephone,
                    'return_url'    => $this->returnUrl . "?ref={$reference}",
                    'callback_url'  => config('app.url') . '/api/v1/paiements/webhook',
                ]);

            if ($reponse->successful() && $reponse->json('statut') === true) {
                return [
                    'success'        => true,
                    'payment_url'    => $reponse->json('url'),
                    'token_paiement' => $reponse->json('token'),
                    'message'        => 'Session de paiement créée.',
                ];
            }

            Log::warning('MoneyFusion: initierPaiement échoué', [
                'reference' => $reference,
                'reponse'   => $reponse->json() ?? $reponse->body(),
            ]);

            return [
                'success'        => false,
                'payment_url'    => null,
                'token_paiement' => null,
                'message'        => $reponse->json('message') ?? 'Erreur lors de la création du paiement.',
            ];

        } catch (\Throwable $e) {
            Log::error('MoneyFusion: exception initierPaiement', ['error' => $e->getMessage()]);
            return ['success' => false, 'payment_url' => null, 'token_paiement' => null, 'message' => 'Erreur réseau.'];
        }
    }

    /**
     * Vérifie le statut d'un paiement via son token MoneyFusion.
     *
     * @return array{success: bool, statut: string, message: string}
     *   statut: 'pending' | 'paid' | 'failed' | 'cancelled'
     */
    public function verifierStatut(string $tokenPaiement): array
    {
        if (!$this->estConfigure()) {
            return ['success' => false, 'statut' => 'pending', 'message' => 'MoneyFusion non configuré.'];
        }

        try {
            $reponse = Http::withToken($this->token)
                ->post("{$this->baseUrl}/checkStatus", [
                    'token' => $tokenPaiement,
                ]);

            if (!$reponse->successful()) {
                return ['success' => false, 'statut' => 'pending', 'message' => 'Impossible de vérifier le statut.'];
            }

            $data   = $reponse->json();
            $statut = match(strtolower($data['statut'] ?? '')) {
                'paid', 'success', 'successful' => 'paid',
                'failed', 'error'               => 'failed',
                'cancelled', 'cancel'           => 'cancelled',
                default                         => 'pending',
            };

            return [
                'success' => true,
                'statut'  => $statut,
                'message' => $data['message'] ?? '',
            ];

        } catch (\Throwable $e) {
            Log::error('MoneyFusion: exception verifierStatut', ['error' => $e->getMessage()]);
            return ['success' => false, 'statut' => 'pending', 'message' => 'Erreur réseau.'];
        }
    }
}
