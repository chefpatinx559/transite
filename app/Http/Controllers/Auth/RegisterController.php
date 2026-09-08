<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    /**
     * Affiche la page d'inscription.
     */
    public function index(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Traite le formulaire d'inscription.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:191|unique:users,email',
            'whatsapp' => 'nullable|string|max:25',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'client',
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect('/espace-client');
    }
}
