<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuoteAdminController extends Controller
{
    /**
     * Liste paginée des demandes de devis avec filtre par statut.
     */
    public function index(Request $request): Response
    {
        $query = QuoteRequest::with('user')
            ->orderByDesc('created_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $quotes = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Quotes/Index', [
            'quotes' => $quotes,
            'filters' => $request->only('status'),
        ]);
    }

    /**
     * Détail d'une demande de devis.
     */
    public function show(QuoteRequest $quote): Response
    {
        return Inertia::render('Admin/Quotes/Show', [
            'quote' => $quote->load('user'),
        ]);
    }

    /**
     * Met à jour le statut d'une demande de devis.
     */
    public function updateStatus(Request $request, QuoteRequest $quote): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,in_review,quoted,won,lost',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $quote->update($validated);

        return redirect()->back()->with('success', 'Devis mis à jour.');
    }
}
