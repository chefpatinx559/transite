<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || Auth::user()->role !== 'super_admin') {
            if ($request->expectsJson()) {
                abort(403, 'Réservé aux super administrateurs.');
            }

            return redirect('/');
        }

        return $next($request);
    }
}
