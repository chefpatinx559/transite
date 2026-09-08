<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    /**
     * Affiche la page de connexion.
     */
    public function index(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Traite le formulaire de connexion.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Identifiants incorrects. Veuillez réessayer.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended('/espace-client');
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
