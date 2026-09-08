<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderAdminController extends Controller
{
    /**
     * Liste paginée des commandes avec filtre par statut.
     */
    public function index(Request $request): Response
    {
        $query = Order::with(['user', 'items'])
            ->orderByDesc('created_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only('status'),
        ]);
    }

    /**
     * Détail d'une commande.
     */
    public function show(Order $order): Response
    {
        return Inertia::render('Admin/Orders/Show', [
            'order' => $order->load(['user', 'items.product']),
        ]);
    }

    /**
     * Met à jour le statut d'une commande.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled,refunded',
        ]);

        $order->update(['status' => $validated['status']]);

        // Enregistre les dates de transition si pertinent
        if ($validated['status'] === 'shipped' && ! $order->shipped_at) {
            $order->update(['shipped_at' => now()]);
        }

        if ($validated['status'] === 'delivered' && ! $order->delivered_at) {
            $order->update(['delivered_at' => now()]);
        }

        return redirect()->back()->with('success', 'Statut de la commande mis à jour.');
    }
}
