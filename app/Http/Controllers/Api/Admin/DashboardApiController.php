<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\QuoteRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardApiController extends Controller
{
    /**
     * Retourne les statistiques et données du tableau de bord admin.
     */
    public function index(): JsonResponse
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // ── Stats ──────────────────────────────────────────────────────────
        $stats = [
            'users_count'    => User::count(),
            'orders_today'   => Order::whereDate('created_at', $today)->count(),
            'revenue_month'  => (float) Order::where('payment_status', 'paid')
                ->where('created_at', '>=', $startOfMonth)
                ->sum('total'),
            'quotes_new'     => QuoteRequest::where('status', 'new')->count(),
            'products_count' => Product::count(),
            'posts_count'    => Post::count(),
        ];

        // ── Orders chart : 7 derniers jours ───────────────────────────────
        $ordersChart = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            $total = Order::where('payment_status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('total');

            return [
                'date'  => $date->format('Y-m-d'),
                'total' => (float) $total,
            ];
        })->values();

        // ── Recent orders ──────────────────────────────────────────────────
        $recentOrders = Order::orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'customer_name', 'total', 'status', 'created_at'])
            ->map(fn (Order $o) => [
                'id'            => $o->id,
                'customer_name' => $o->customer_name,
                'total'         => $o->total,
                'status'        => $o->status,
                'created_at'    => $o->created_at,
            ]);

        // ── Recent quotes ──────────────────────────────────────────────────
        $recentQuotes = QuoteRequest::orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'name', 'whatsapp', 'product_description', 'status', 'created_at'])
            ->map(fn (QuoteRequest $q) => [
                'id'                  => $q->id,
                'name'                => $q->name,
                'whatsapp'            => $q->whatsapp,
                'product_description' => mb_substr($q->product_description ?? '', 0, 50),
                'status'              => $q->status,
                'created_at'          => $q->created_at,
            ]);

        return response()->json([
            'stats'         => $stats,
            'orders_chart'  => $ordersChart,
            'recent_orders' => $recentOrders,
            'recent_quotes' => $recentQuotes,
        ]);
    }
}
