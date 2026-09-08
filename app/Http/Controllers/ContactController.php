<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Enregistre un message de contact.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|max:191',
            'phone' => 'nullable|string|max:25',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessage::create($validated);

        return redirect()->back()->with('success', 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.');
    }
}
