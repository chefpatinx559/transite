<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Tableau de bord de l'espace client.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'total_spent' => Order::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('total'),
            'pending_orders' => Order::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
        ];

        $recentOrders = Order::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'order_number', 'total', 'status', 'created_at']);

        return Inertia::render('Client/Dashboard', [
            'stats'        => $stats,
            'recentOrders' => $recentOrders,
            'user'         => $user,
        ]);
    }

    /**
     * Historique des commandes de l'utilisateur connecté.
     */
    public function orders(Request $request): Response
    {
        $query = Order::withCount('items')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return Inertia::render('Client/Orders', [
            'orders'  => $query->paginate(10)->withQueryString(),
            'filters' => ['status' => $request->input('status', '')],
        ]);
    }
}
