<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Enregistre une inscription à la newsletter.
     */
    public function subscribe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:191|unique:newsletter_subscribers,email',
            'name' => 'nullable|string|max:100',
            'source' => 'nullable|in:footer,popup,blog,checkout',
        ]);

        NewsletterSubscriber::create([
            'email' => $validated['email'],
            'name' => $validated['name'] ?? null,
            'source' => $validated['source'] ?? 'footer',
        ]);

        return redirect()->back()->with('success', 'Merci ! Votre inscription à la newsletter a bien été prise en compte.');
    }
}
