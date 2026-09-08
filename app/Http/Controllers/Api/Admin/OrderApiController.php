<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderApiController extends Controller
{
    /**
     * Liste paginée des commandes avec filtre de statut.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with('user')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_email', 'like', "%{$q}%");
            });
        }

        return response()->json($query->paginate(15));
    }

    /**
     * Retourne une commande complète avec ses articles et produits associés.
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['user', 'items.product']);

        return response()->json($order);
    }

    /**
     * Met à jour le statut d'une commande et gère shipped_at / delivered_at.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled,refunded',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'shipped' && is_null($order->shipped_at)) {
            $data['shipped_at'] = Carbon::now();
        }

        if ($request->status === 'delivered' && is_null($order->delivered_at)) {
            $data['delivered_at'] = Carbon::now();
        }

        $order->update($data);

        return response()->json($order->load(['user', 'items.product']));
    }
}
