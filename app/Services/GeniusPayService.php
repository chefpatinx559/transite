<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeniusPayService
{
    private ?string $apiKey;
    private ?string $apiSecret;
    private string  $baseUrl;

    public function __construct()
    {
        $this->apiKey    = config('services.geniuspay.api_key')    ?? '';
        $this->apiSecret = config('services.geniuspay.api_secret') ?? '';
        $this->baseUrl   = config('services.geniuspay.base_url')   ?? 'https://geniuspay.ci/api/v1/merchant';
    }

    /**
     * Crée un paiement et retourne l'URL checkout GeniusPay.
     */
    public function createPayment(array $params): array
    {
        $response = $this->request('POST', '/payments', $params);

        if (! $response->successful() || ! $response->json('success')) {
            $error = $response->json('error.message', 'Erreur GeniusPay inconnue');
            Log::error('GeniusPay createPayment error', [
                'status'   => $response->status(),
                'response' => $response->json(),
            ]);
            throw new \RuntimeException("Paiement GeniusPay impossible : {$error}");
        }

        return $response->json('data');
    }

    /**
     * Récupère les détails d'une transaction par référence.
     */
    public function getPayment(string $reference): array
    {
        $response = $this->request('GET', "/payments/{$reference}");

        if (! $response->successful()) {
            throw new \RuntimeException("Transaction {$reference} introuvable");
        }

        return $response->json('data');
    }

    /**
     * Vérifie la signature d'un webhook.
     */
    public function verifyWebhookSignature(string $signature, string $timestamp, string $rawPayload): bool
    {
        $secret   = config('services.geniuspay.webhook_secret');
        $data     = $timestamp . '.' . $rawPayload;
        $expected = hash_hmac('sha256', $data, $secret);

        return hash_equals($expected, $signature);
    }

    private function request(string $method, string $path, array $body = []): Response
    {
        $client = Http::withHeaders([
            'X-API-Key'    => $this->apiKey,
            'X-API-Secret' => $this->apiSecret,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->timeout(30);

        return match (strtoupper($method)) {
            'POST'  => $client->post($this->baseUrl . $path, $body),
            default => $client->get($this->baseUrl . $path),
        };
    }
}
