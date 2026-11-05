<?php

return [
    'ssr' => [
        'enabled' => (bool) env('INERTIA_SSR_ENABLED', true),

        // ⚠️ URL de BASE uniquement : Inertia\Ssr\HttpGateway ajoute lui-même "/render".
        // L'ancienne valeur ".../render" produisait ".../render/render" → SSR toujours en échec
        // (requête HTTP perdue à chaque page + repli en rendu 100 % client = LCP tardif).
        'url' => env('INERTIA_SSR_URL', 'http://127.0.0.1:13714'),

        // Ne tente pas d'appel HTTP si le bundle SSR n'a pas été buildé.
        'ensure_bundle_exists' => (bool) env('INERTIA_SSR_ENSURE_BUNDLE_EXISTS', true),

        'throw_on_error' => (bool) env('INERTIA_SSR_THROW_ON_ERROR', false),
    ],
];
