<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\GeniusPayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeniusPayWebhookController extends Controller
{
    public function __construct(private GeniusPayService $geniusPay) {}

    public function handle(Request $request): JsonResponse
    {
        $signature = $request->header('X-Webhook-Signature', '');
        $timestamp = $request->header('X-Webhook-Timestamp', '');
        $event     = $request->header('X-Webhook-Event', '');
        $rawBody   = $request->getContent();

        // 1. Vérifier la signature
        if (! $this->geniusPay->verifyWebhookSignature($signature, $timestamp, $rawBody)) {
            Log::warning('GeniusPay webhook: signature invalide');
            return response()->json(['status' => 401, 'detail' => 'Invalid signature'], 401);
        }

        // 2. Vérifier le timestamp (protection replay attack — 5 min)
        if (abs(time() - (int) $timestamp) > 300) {
            Log::warning('GeniusPay webhook: timestamp trop vieux', ['timestamp' => $timestamp]);
            return response()->json(['status' => 400, 'detail' => 'Timestamp too old'], 400);
        }

        $payload  = $request->all();
        $data     = $payload['data'] ?? [];
        $metadata = $data['metadata'] ?? [];

        Log::info('GeniusPay webhook reçu', ['event' => $event, 'reference' => $data['reference'] ?? '—']);

        match ($event) {
            'payment.success' => $this->handlePaymentSuccess($data, $metadata),
            'payment.failed'  => $this->handlePaymentFailed($data, $metadata),
            default           => null,
        };

        return response()->json(['received' => true]);
    }

    private function handlePaymentSuccess(array $data, array $metadata): void
    {
        $order = $this->findOrder($data, $metadata);
        if (! $order) return;

        $order->update([
            'status'         => 'paid',
            'payment_status' => 'paid',
            'payment_ref'    => $data['reference'] ?? $order->payment_ref,
        ]);

        Log::info("Commande #{$order->order_number} marquée payée via GeniusPay");
    }

    private function handlePaymentFailed(array $data, array $metadata): void
    {
        $order = $this->findOrder($data, $metadata);
        if (! $order) return;

        $order->update([
            'payment_status' => 'failed',
        ]);

        Log::info("Commande #{$order->order_number} — paiement échoué");
    }

    private function findOrder(array $data, array $metadata): ?Order
    {
        // Priorité : token dans les metadata
        if (! empty($metadata['order_token'])) {
            $order = Order::where('token', $metadata['order_token'])->first();
            if ($order) return $order;
        }

        // Fallback : référence GeniusPay stockée dans payment_ref
        if (! empty($data['reference'])) {
            $order = Order::where('payment_ref', $data['reference'])->first();
            if ($order) return $order;
        }

        Log::warning('GeniusPay webhook: commande introuvable', [
            'metadata'  => $metadata,
            'reference' => $data['reference'] ?? null,
        ]);

        return null;
    }
}
