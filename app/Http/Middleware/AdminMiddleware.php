<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Vérifie que l'utilisateur est connecté et possède le rôle admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            if ($request->expectsJson()) {
                abort(403, 'Accès non autorisé.');
            }

            return redirect('/');
        }

        return $next($request);
    }
}
