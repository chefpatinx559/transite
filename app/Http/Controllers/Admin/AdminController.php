<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\QuoteRequest;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * Tableau de bord administration.
     */
    public function index(): Response
    {
        $stats = [
            'total_users' => User::count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'revenue_month' => Order::where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total'),
            'new_quotes' => QuoteRequest::where('status', 'new')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
        ]);
    }
}
