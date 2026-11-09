<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->is('/') || $request->is('services') || $request->is('boutique') || $request->is('a-propos') || $request->is('contact') || $request->is('faq') || $request->is('formations') || $request->is('blog') || $request->is('importer')) {
            $currentUrl = url($request->getPathInfo());
            $response->headers->set('Link', implode(', ', [
                '<https://netspring.business/llms.txt>; rel="describedby"; type="text/plain"',
                "<{$currentUrl}>; rel=\"alternate\"; type=\"text/markdown\"",
            ]));
        }

        return $response;
    }
}
