<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmation;
use App\Models\Formation;
use App\Models\FormationRegistration;
use App\Models\Order;
use App\Services\GeniusPayService;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GeniusPayWebhookController extends Controller
{
    public function __construct(
        private GeniusPayService $geniusPay,
        private TelegramService  $telegram,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $signature = $request->header('X-Webhook-Signature', '');
        $timestamp = $request->header('X-Webhook-Timestamp', '');
        $event     = $request->header('X-Webhook-Event', '');
        $rawBody   = $request->getContent();

        // Vérifier la signature
        if (! $this->geniusPay->verifyWebhookSignature($signature, $timestamp, $rawBody)) {
            Log::warning('GeniusPay webhook: signature invalide');
            return response()->json(['status' => 401, 'detail' => 'Invalid signature'], 401);
        }

        // Protection replay attack (5 min)
        if (abs(time() - (int) $timestamp) > 300) {
            return response()->json(['status' => 400, 'detail' => 'Timestamp too old'], 400);
        }

        $payload  = $request->all();
        $data     = $payload['data'] ?? [];
        $metadata = $data['metadata'] ?? [];

        Log::info('GeniusPay webhook reçu', ['event' => $event, 'ref' => $data['reference'] ?? '—']);

        match ($event) {
            'payment.success' => $this->handlePaymentSuccess($data, $metadata),
            'payment.failed'  => $this->handlePaymentFailed($data, $metadata),
            default           => null,
        };

        return response()->json(['received' => true]);
    }

    // ── Commande boutique ─────────────────────────────────────────────────────

    private function handlePaymentSuccess(array $data, array $metadata): void
    {
        // Commande boutique
        $order = $this->findOrder($data, $metadata);
        if ($order && $order->payment_status !== 'paid') {
            $order->update([
                'status'         => 'paid',
                'payment_status' => 'paid',
                'payment_ref'    => $data['reference'] ?? $order->payment_ref,
            ]);

            // Email + Telegram si pas déjà envoyés par paymentSuccess()
            try {
                Mail::to($order->customer_email)
                    ->send(new OrderConfirmation($order->load('items')));
            } catch (\Exception $e) {
                Log::warning('Webhook: order email failed', ['error' => $e->getMessage()]);
            }

            $this->telegram->notifyNewOrder($order->load('items'));
            Log::info("Commande #{$order->order_number} payée via webhook GeniusPay");
            return;
        }

        // Inscription formation
        $registration = $this->findRegistration($metadata);
        if ($registration && $registration->payment_status !== 'paid') {
            $registration->update(['status' => 'confirmed', 'payment_status' => 'paid']);
            $formation = $registration->formation;

            try {
                Mail::to($registration->email)
                    ->send(new \App\Mail\FormationRegistrationConfirmation($registration->fresh(), $formation));
            } catch (\Exception $e) {
                Log::warning('Webhook: formation email failed', ['error' => $e->getMessage()]);
            }

            $this->telegram->notifyNewFormationRegistration($registration->load('formation'));
            Log::info("Inscription formation #{$registration->id} confirmée via webhook");
        }
    }

    private function handlePaymentFailed(array $data, array $metadata): void
    {
        $order = $this->findOrder($data, $metadata);
        if ($order) {
            $order->update(['payment_status' => 'failed']);
            return;
        }

        $registration = $this->findRegistration($metadata);
        if ($registration) {
            $registration->update(['payment_status' => 'failed']);
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function findOrder(array $data, array $metadata): ?Order
    {
        if (! empty($metadata['order_token'])) {
            $order = Order::where('token', $metadata['order_token'])->first();
            if ($order) return $order;
        }
        if (! empty($data['reference'])) {
            return Order::where('payment_ref', $data['reference'])->first();
        }
        return null;
    }

    private function findRegistration(array $metadata): ?FormationRegistration
    {
        if (! empty($metadata['registration_token'])) {
            return FormationRegistration::with('formation')
                ->where('token', $metadata['registration_token'])->first();
        }
        return null;
    }
}
