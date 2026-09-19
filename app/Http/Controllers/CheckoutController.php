<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Mail\OrderConfirmation;
use App\Services\GeniusPayService;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    private const CART_KEY = 'cart';

    public function __construct(
        private GeniusPayService $geniusPay,
        private TelegramService  $telegram,
    ) {}

    public function index(): Response|RedirectResponse
    {
        $cart = session(self::CART_KEY, []);

        if (empty($cart)) {
            return redirect('/panier')->with('error', 'Votre panier est vide.');
        }

        [$items, $subtotal] = $this->buildCartItems($cart);

        // Calcul des modes de livraison disponibles
        $productIds = array_keys($cart);
        $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

        $modes = [];
        $modeKeys = [
            'air_express' => ['label' => 'Avion Express', 'delay' => '~1 semaine',   'field' => 'shipping_air_express'],
            'air_normal'  => ['label' => 'Avion Normal',  'delay' => '~3 semaines',  'field' => 'shipping_air_normal'],
            'sea'         => ['label' => 'Bateau',         'delay' => '45–60 jours', 'field' => 'shipping_sea'],
        ];
        foreach ($modeKeys as $key => $info) {
            $totalCost = 0;
            $available = true;
            foreach ($cart as $productId => $item) {
                $product = $products->get($productId);
                $qty = $item['quantity'] ?? 1;
                if (!$product || $product->{$info['field']} === null) {
                    $available = false;
                    break;
                }
                $totalCost += $product->{$info['field']} * $qty;
            }
            if ($available) {
                $modes[] = [
                    'mode'  => $key,
                    'label' => $info['label'],
                    'delay' => $info['delay'],
                    'cost'  => $totalCost,
                ];
            }
        }

        return Inertia::render('Checkout', [
            'items'         => $items,
            'subtotal'      => $subtotal,
            'total'         => $subtotal,
            'shippingModes' => $modes,
        ]);
    }

    public function process(Request $request): RedirectResponse|HttpResponse
    {
        $cart = session(self::CART_KEY, []);

        if (empty($cart)) {
            return redirect('/panier')->with('error', 'Votre panier est vide.');
        }

        $validated = $request->validate([
            'customer_name'            => 'required|string|max:200',
            'customer_email'           => 'required|email|max:191',
            'customer_phone'           => 'required|string|max:25',
            'shipping_address.address' => 'required|string|max:255',
            'shipping_address.city'    => 'required|string|max:100',
            'shipping_address.country' => 'nullable|string|max:100',
            'notes'                    => 'nullable|string|max:1000',
            'shipping_mode'            => 'required|in:air_express,air_normal,sea',
        ]);

        // Recalcul depuis la BDD
        $subtotal  = 0.0;
        $lineItems = [];

        foreach ($cart as $item) {
            $product = Product::findOrFail((int) $item['product_id']);
            $qty     = (int) $item['quantity'];
            $price   = (float) $product->price;
            $sub     = $price * $qty;
            $subtotal += $sub;

            $firstImage = $product->images[0] ?? null;
            $image      = is_array($firstImage) ? ($firstImage['url'] ?? null) : $firstImage;

            $lineItems[] = [
                'product_id' => $product->id,
                'course_id'  => null,
                'item_type'  => 'product',
                'name'       => $product->name,
                'price'      => $price,
                'quantity'   => $qty,
                'subtotal'   => $sub,
                'image'      => $image,
            ];
        }

        // Recalcul du coût de livraison depuis la BDD
        $shippingField = match($validated['shipping_mode']) {
            'air_express' => 'shipping_air_express',
            'air_normal'  => 'shipping_air_normal',
            'sea'         => 'shipping_sea',
        };
        $shippingCost = 0.0;
        foreach ($lineItems as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->{$shippingField} !== null) {
                $shippingCost += $product->{$shippingField} * $item['quantity'];
            }
        }

        $total = $subtotal + $shippingCost;

        // Montant minimum GeniusPay : 200 XOF
        if ($total < 200) {
            return back()->with('error', 'Le montant minimum de commande est de 200 FCFA.');
        }

        // Créer la commande AVANT le paiement
        $order = Order::create([
            'user_id'          => Auth::id(),
            'order_number'     => Order::generateOrderNumber(),
            'status'           => 'pending',
            'subtotal'         => $subtotal,
            'shipping_cost'    => $shippingCost,
            'shipping_mode'    => $validated['shipping_mode'],
            'discount_amount'  => 0.00,
            'total'            => $total,
            'currency'         => 'XOF',
            'payment_method'   => 'geniuspay',
            'payment_status'   => 'unpaid',
            'customer_name'    => $validated['customer_name'],
            'customer_email'   => $validated['customer_email'],
            'customer_phone'   => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'notes'            => $validated['notes'] ?? null,
        ]);

        foreach ($lineItems as $line) {
            OrderItem::create(array_merge(['order_id' => $order->id], $line));
        }

        // Appel GeniusPay (email + Telegram envoyés dans paymentSuccess après confirmation)
        try {
            $payment = $this->geniusPay->createPayment([
                'amount'      => (int) round($total),
                'currency'    => 'XOF',
                'description' => "Commande {$order->order_number} — NETSPRING",
                'customer'    => [
                    'name'  => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'success_url' => route('payment.success', ['order' => $order->token]),
                'error_url'   => route('payment.error',   ['order' => $order->token]),
                'metadata'    => [
                    'order_id'    => $order->id,
                    'order_token' => $order->token,
                    'order_number'=> $order->order_number,
                ],
            ]);

            // Sauvegarder la référence GeniusPay
            $order->update(['payment_ref' => $payment['reference'] ?? null]);

            $checkoutUrl = $payment['checkout_url'] ?? $payment['payment_url'] ?? null;

            if (! $checkoutUrl) {
                throw new \RuntimeException('Aucune URL de paiement retournée par GeniusPay');
            }

            // Vider le panier seulement après redirection réussie vers GeniusPay
            session()->forget(self::CART_KEY);

            return Inertia::location($checkoutUrl);

        } catch (\Exception $e) {
            Log::error('GeniusPay checkout error', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            // Fallback : page d'erreur paiement
            return redirect()->route('payment.error', ['order' => $order->token])
                ->with('error', 'Impossible d\'initier le paiement. Réessayez ou contactez-nous.');
        }
    }

    public function paymentSuccess(Request $request): Response|RedirectResponse
    {
        $token = $request->query('order');
        if (! $token) {
            return redirect('/boutique');
        }

        $order = Order::with('items')->where('token', $token)->firstOrFail();

        // Vérification paiement via API GeniusPay
        if ($order->payment_ref && $order->payment_status !== 'paid') {
            try {
                $payment = $this->geniusPay->getPayment($order->payment_ref);
                if ($payment['status'] === 'completed') {
                    $order->update(['status' => 'paid', 'payment_status' => 'paid']);

                    // Email de confirmation (seulement après paiement confirmé)
                    try {
                        Mail::to($order->customer_email)
                            ->send(new OrderConfirmation($order->load('items')));
                    } catch (\Exception $e) {
                        Log::warning('Order confirmation email failed', ['error' => $e->getMessage()]);
                    }

                    // Telegram après paiement confirmé
                    $this->telegram->notifyNewOrder($order->load('items'));
                }
            } catch (\Exception $e) {
                Log::warning('GeniusPay verification failed', ['error' => $e->getMessage()]);
            }
        }

        return Inertia::render('Commande/Confirmation', ['order' => $order->fresh('items')]);
    }

    public function paymentError(Request $request): Response|RedirectResponse
    {
        $token = $request->query('order');
        if (! $token) {
            return redirect('/boutique');
        }

        $order = Order::where('token', $token)->firstOrFail();
        $order->update(['payment_status' => 'failed']);

        return Inertia::render('Commande/PaymentError', ['order' => $order]);
    }

    public function confirmation(string $token): Response
    {
        $order = Order::with('items')->where('token', $token)->firstOrFail();

        return Inertia::render('Commande/Confirmation', ['order' => $order]);
    }

    private function buildCartItems(array $cart): array
    {
        $subtotal = 0.0;
        $items    = array_values(array_map(function (array $item) use (&$subtotal) {
            $sub = $item['price'] * $item['quantity'];
            $subtotal += $sub;
            return array_merge($item, ['subtotal' => $sub]);
        }, $cart));

        return [$items, $subtotal];
    }
}
