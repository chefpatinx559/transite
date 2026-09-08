<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuoteApiController extends Controller
{
    /**
     * Liste paginée des devis avec filtre de statut.
     */
    public function index(Request $request): JsonResponse
    {
        $query = QuoteRequest::orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('whatsapp', 'like', "%{$q}%")
                    ->orWhere('product_description', 'like', "%{$q}%");
            });
        }

        return response()->json($query->paginate(15));
    }

    /**
     * Retourne un devis complet.
     */
    public function show(QuoteRequest $quote): JsonResponse
    {
        return response()->json($quote->load('user'));
    }

    /**
     * Met à jour le statut d'un devis et les notes admin.
     */
    public function updateStatus(Request $request, QuoteRequest $quote): JsonResponse
    {
        $request->validate([
            'status'      => 'required|in:new,in_review,quoted,won,lost',
            'admin_notes' => 'nullable|string',
        ]);

        $quote->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes ?? $quote->admin_notes,
        ]);

        return response()->json($quote->load('user'));
    }
}
