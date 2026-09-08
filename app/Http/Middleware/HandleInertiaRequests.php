<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $cart      = session('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        return [
            ...parent::share($request),
            'flash' => [
                'success' => session('success'),
                'error'   => session('error'),
            ],
            'cartCount' => $cartCount,
        ];
    }
}
