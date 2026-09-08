<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\DashboardApiController;
use App\Http\Controllers\Api\Admin\ProductApiController;
use App\Http\Controllers\Api\Admin\OrderApiController;
use App\Http\Controllers\Api\Admin\PostApiController;
use App\Http\Controllers\Api\Admin\QuoteApiController;
use App\Http\Controllers\Api\Admin\UserApiController;
use App\Http\Controllers\Api\Admin\ProfileApiController;

// ──────────────────────────────────────────────
// AUTH (public)
// ──────────────────────────────────────────────
Route::post('/admin/login',  [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/admin/me',      [AuthController::class, 'me'])->middleware('auth:sanctum');

// ──────────────────────────────────────────────
// ADMIN API (token sanctum + rôle admin)
// ──────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    // Produits
    Route::get('/products',               [ProductApiController::class, 'index']);
    Route::post('/products',              [ProductApiController::class, 'store']);
    Route::get('/products/{product}',     [ProductApiController::class, 'show']);
    Route::post('/products/{product}',    [ProductApiController::class, 'update']); // POST avec _method=PUT pour FormData
    Route::delete('/products/{product}',  [ProductApiController::class, 'destroy']);
    Route::patch('/products/{product}/toggle', [ProductApiController::class, 'toggle']);

    // Commandes
    Route::get('/orders',                 [OrderApiController::class, 'index']);
    Route::get('/orders/{order}',         [OrderApiController::class, 'show']);
    Route::patch('/orders/{order}/status',[OrderApiController::class, 'updateStatus']);

    // Articles
    Route::get('/posts',                  [PostApiController::class, 'index']);
    Route::post('/posts',                 [PostApiController::class, 'store']);
    Route::get('/posts/{post}',           [PostApiController::class, 'show']);
    Route::post('/posts/{post}',          [PostApiController::class, 'update']); // FormData (cover_image)
    Route::delete('/posts/{post}',        [PostApiController::class, 'destroy']);

    // Devis
    Route::get('/quotes',                 [QuoteApiController::class, 'index']);
    Route::get('/quotes/{quote}',         [QuoteApiController::class, 'show']);
    Route::patch('/quotes/{quote}/status',[QuoteApiController::class, 'updateStatus']);

    // Utilisateurs
    Route::get('/users', [UserApiController::class, 'index']);
    Route::middleware('super_admin')->group(function () {
        Route::post('/users',              [UserApiController::class, 'store']);
        Route::patch('/users/{user}/role', [UserApiController::class, 'updateRole']);
    });

    // Profil (changement de mot de passe)
    Route::patch('/profile/password', [ProfileApiController::class, 'changePassword']);

    // Formations
    Route::get('/formations',                           [\App\Http\Controllers\Api\Admin\FormationApiController::class, 'index']);
    Route::post('/formations',                          [\App\Http\Controllers\Api\Admin\FormationApiController::class, 'store']);
    Route::get('/formations/{formation}',               [\App\Http\Controllers\Api\Admin\FormationApiController::class, 'show']);
    Route::post('/formations/{formation}',              [\App\Http\Controllers\Api\Admin\FormationApiController::class, 'update']);
    Route::delete('/formations/{formation}',            [\App\Http\Controllers\Api\Admin\FormationApiController::class, 'destroy']);
    Route::patch('/formations/{formation}/toggle',      [\App\Http\Controllers\Api\Admin\FormationApiController::class, 'toggle']);
    Route::get('/formations/{formation}/inscriptions',  [\App\Http\Controllers\Api\Admin\FormationApiController::class, 'registrations']);
    Route::patch('/formations/{formation}/inscriptions/{registration}', [\App\Http\Controllers\Api\Admin\FormationApiController::class, 'updateRegistration']);

    // Catégories (pour les formulaires — filtrées par type)
    Route::get('/categories', function () {
        $type = request('type'); // ?type=produit | blog | formation
        $query = \App\Models\Category::orderBy('name');
        if ($type) $query->where('type', $type);
        return response()->json($query->get());
    });
});
