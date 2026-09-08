<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    /**
     * Enregistre une demande de devis.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|max:191',
            'whatsapp' => 'required|string|max:25',
            'city' => 'nullable|string|max:100',
            'product_description' => 'required|string|max:5000',
            'quantity' => 'nullable|string|max:100',
            'budget' => 'nullable|string|max:100',
            'source_country' => 'nullable|string|max:50',
            'experience_level' => 'nullable|in:debutant,intermediaire,avance',
            'services' => 'nullable|array',
            'services.*' => 'string|max:100',
        ]);

        QuoteRequest::create(array_merge($validated, [
            'user_id' => Auth::id(),
            'status' => 'new',
        ]));

        return redirect()->back()->with('success', 'Votre demande de devis a bien été envoyée. Notre équipe vous contactera sous 24h.');
    }
}
