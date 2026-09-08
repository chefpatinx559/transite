<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $token;
    private string $chatId;
    private string $baseUrl;

    public function __construct()
    {
        $this->token   = config('services.telegram.token', '');
        $this->chatId  = config('services.telegram.chat_id', '');
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    public function send(string $message): void
    {
        if (empty($this->token) || empty($this->chatId)) {
            return;
        }

        try {
            Http::timeout(5)->post("{$this->baseUrl}/sendMessage", [
                'chat_id'    => $this->chatId,
                'text'       => $message,
                'parse_mode' => 'HTML',
            ]);
        } catch (\Exception $e) {
            Log::warning('Telegram notification failed', ['error' => $e->getMessage()]);
        }
    }

    public function notifyNewOrder(\App\Models\Order $order): void
    {
        $n = "\n";
        $items = $order->items->map(fn ($i) => "  • {$i->name} × {$i->quantity}")->join($n);
        $price = number_format($order->total, 0, ',', ' ') . ' FCFA';

        $msg = "🛒 <b>Nouvelle commande !</b>{$n}{$n}"
             . "📋 <b>N°</b> {$order->order_number}{$n}"
             . "👤 <b>Client</b> {$order->customer_name}{$n}"
             . "📱 <b>Tél.</b> " . ($order->customer_phone ?? '—') . "{$n}"
             . "📧 <b>Email</b> {$order->customer_email}{$n}"
             . "📍 <b>Ville</b> " . ($order->shipping_address['city'] ?? '—') . "{$n}{$n}"
             . "🧾 <b>Articles</b>{$n}{$items}{$n}{$n}"
             . "💰 <b>Total</b> {$price}{$n}"
             . "💳 <b>Paiement</b> " . strtoupper($order->payment_method ?? '—');

        $this->send($msg);
    }

    public function notifyNewFormationRegistration(\App\Models\FormationRegistration $registration): void
    {
        $n         = "\n";
        $formation = $registration->formation;
        $price     = $formation->is_free
            ? 'Gratuite'
            : number_format($formation->price, 0, ',', ' ') . ' FCFA';
        $type = $formation->type === 'online' ? '🌐 En ligne' : '📍 Présentielle';

        $msg = "🎓 <b>Nouvelle inscription formation !</b>{$n}{$n}"
             . "📚 <b>Formation</b> {$formation->title}{$n}"
             . "🏷️ <b>Type</b> {$type}{$n}"
             . "💰 <b>Prix</b> {$price}{$n}{$n}"
             . "👤 <b>Participant</b> {$registration->name}{$n}"
             . "📱 <b>Tél.</b> " . ($registration->phone ?? '—') . "{$n}"
             . "📧 <b>Email</b> {$registration->email}";

        if ($registration->notes) {
            $msg .= "{$n}💬 <b>Message</b> {$registration->notes}";
        }

        $msg .= "{$n}{$n}✅ <b>Statut</b> " . ucfirst($registration->status);

        $this->send($msg);
    }
}
