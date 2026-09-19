<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:191',
        ]);

        $apiKey = config('services.brevo.api_key');
        $listId = config('services.brevo.list_id');

        $payload = [
            'email'         => $request->email,
            'updateEnabled' => true,
            'attributes'    => ['SOURCE' => 'NETSPRING_FOOTER'],
        ];
        if ($listId) {
            $payload['listIds'] = [(int) $listId];
        }

        try {
            $response = Http::withHeaders([
                'api-key'      => $apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post('https://api.brevo.com/v3/contacts', $payload);

            if ($response->successful() || $response->status() === 204) {
                return response()->json(['message' => 'Inscription réussie !']);
            }

            // Contact déjà existant → succès silencieux
            if ($response->status() === 400 &&
                str_contains($response->body(), 'Contact already exist')) {
                return response()->json(['message' => 'Vous êtes déjà inscrit(e).']);
            }

            Log::warning('Brevo subscribe error', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['message' => 'Une erreur est survenue. Réessayez.'], 500);

        } catch (\Exception $e) {
            Log::error('Brevo subscribe exception', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Une erreur est survenue. Réessayez.'], 500);
        }
    }
}
