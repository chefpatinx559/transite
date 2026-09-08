<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

        return Inertia::render('Client/Dashboard', [
            'stats' => $stats,
        ]);
    }

    /**
     * Historique des commandes de l'utilisateur connecté.
     */
    public function orders(): Response
    {
        $orders = Order::with('items')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return Inertia::render('Client/Orders', [
            'orders' => $orders,
        ]);
    }
}
