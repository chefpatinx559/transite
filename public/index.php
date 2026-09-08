<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| NETSPRING — index.php pour hébergement mutualisé Hostinger
|--------------------------------------------------------------------------
| Ce fichier est placé dans public_html/.
| Laravel est dans le dossier parent (laravel/).
|
| En local : __DIR__ = /public → ../ = racine Laravel ← fonctionne aussi
| Sur Hostinger : __DIR__ = public_html → ../ = domains/domaine.ci
|                 Laravel est dans domains/domaine.ci/laravel/
*/

// Détecter l'environnement : local ou production
$laravelRoot = is_dir(__DIR__.'/../vendor')
    ? __DIR__.'/..'            // local : racine Laravel directement au dessus
    : __DIR__.'/../laravel';   // Hostinger : dossier laravel/

// Mode maintenance
if (file_exists($maintenance = $laravelRoot.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader
require $laravelRoot.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $laravelRoot.'/bootstrap/app.php';

// Forcer le chemin public sur le dossier courant (public_html)
$app->bind('path.public', fn () => __DIR__);

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
