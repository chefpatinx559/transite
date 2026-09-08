<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\PostAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\QuoteAdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ──────────────────────────────────────────────
// ADMIN SPA — sert index.html pour toutes les routes /admin/*
// ──────────────────────────────────────────────
Route::get('/admin/{any?}', function () {
    $adminIndex = public_path('admin/index.html');
    if (file_exists($adminIndex)) {
        return response()->file($adminIndex);
    }
    return response('<p style="font-family:sans-serif;padding:2rem">
        <strong>Admin en cours de construction.</strong><br>
        Lancez <code>npm run dev</code> dans le dossier <code>admin/</code>
        et accédez à <a href="http://localhost:5173">localhost:5173</a>.
    </p>', 200, ['Content-Type' => 'text/html']);
})->where('any', '.*')->name('admin.spa');

// ──────────────────────────────────────────────
// PAGES VITRINE — M1
// ──────────────────────────────────────────────
Route::get('/', function () {
    $posts = \App\Models\Post::published()
        ->with('category:id,name,slug,color')
        ->orderByDesc('published_at')
        ->limit(4)
        ->get(['id', 'title', 'slug', 'cover_image', 'reading_time', 'published_at', 'category_id']);

    return Inertia::render('Home', ['latestPosts' => $posts]);
})->name('home');
Route::get('/services', fn () => Inertia::render('Services'))->name('services');
Route::get('/a-propos', fn () => Inertia::render('About'))->name('about');
Route::get('/faq', fn () => Inertia::render('Faq'))->name('faq');

Route::get('/importer', fn () => Inertia::render('Importer'))->name('importer');
Route::get('/importer/devis', fn () => Inertia::render('Importer', ['openForm' => true]))->name('importer.devis');
Route::post('/importer/devis', [QuoteController::class, 'store'])->name('quote.store');

Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Formations
Route::get('/formations',                        [\App\Http\Controllers\FormationController::class, 'index'])->name('formations');
Route::get('/formations/{slug}',                 [\App\Http\Controllers\FormationController::class, 'show'])->name('formation.show');
Route::post('/formations/{slug}/inscription',    [\App\Http\Controllers\FormationController::class, 'register'])->name('formation.register');
Route::get('/formation/confirmation/{token}',    [\App\Http\Controllers\FormationController::class, 'confirmation'])->name('formation.confirmation');
Route::get('/formation/paiement/succes',         [\App\Http\Controllers\FormationController::class, 'paymentSuccess'])->name('formation.payment.success');
Route::get('/formation/paiement/echec',          [\App\Http\Controllers\FormationController::class, 'paymentError'])->name('formation.payment.error');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// SEO
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

Route::get('/mentions-legales',             fn () => Inertia::render('Legal/MentionsLegales'))->name('mentions-legales');
Route::get('/politique-de-confidentialite', fn () => Inertia::render('Legal/PolitiqueConfidentialite'))->name('politique-confidentialite');

// ──────────────────────────────────────────────
// BLOG — M2
// ──────────────────────────────────────────────
Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');

// ──────────────────────────────────────────────
// BOUTIQUE — M3
// ──────────────────────────────────────────────
Route::get('/boutique', [ProductController::class, 'index'])->name('boutique.index');
Route::get('/boutique/{slug}', [ProductController::class, 'show'])->name('boutique.show');

// Panier (public, session)
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter', [CartController::class, 'add'])->name('cart.add');
Route::patch('/panier/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/panier', [CartController::class, 'clear'])->name('cart.clear');

// Checkout + paiement GeniusPay
Route::get('/checkout',                              [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout',                             [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/commande/confirmation/{token}',         [CheckoutController::class, 'confirmation'])->name('order.confirmation');
Route::get('/paiement/succes',                       [CheckoutController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/paiement/echec',                        [CheckoutController::class, 'paymentError'])->name('payment.error');

// Webhook GeniusPay (exclu du CSRF via bootstrap/app.php)
Route::post('/webhooks/geniuspay', [\App\Http\Controllers\GeniusPayWebhookController::class, 'handle'])->name('webhooks.geniuspay');

// ──────────────────────────────────────────────
// AUTH
// ──────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [LoginController::class, 'index'])->name('login');
    Route::post('/connexion', [LoginController::class, 'store'])->name('login.store');
    Route::get('/inscription', [RegisterController::class, 'index'])->name('register');
    Route::post('/inscription', [RegisterController::class, 'store'])->name('register.store');
});
Route::post('/deconnexion', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// ──────────────────────────────────────────────
// ESPACE CLIENT — M5
// ──────────────────────────────────────────────
Route::middleware('auth')->prefix('espace-client')->name('client.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/commandes', [DashboardController::class, 'orders'])->name('orders');
    Route::get('/formations', fn () => Inertia::render('Client/Formations'))->name('formations');
});

// ──────────────────────────────────────────────
// ADMIN — M7
// ──────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Articles
    Route::get('/articles', [PostAdminController::class, 'index'])->name('posts.index');
    Route::get('/articles/creer', [PostAdminController::class, 'create'])->name('posts.create');
    Route::post('/articles', [PostAdminController::class, 'store'])->name('posts.store');
    Route::get('/articles/{post}/modifier', [PostAdminController::class, 'edit'])->name('posts.edit');
    Route::put('/articles/{post}', [PostAdminController::class, 'update'])->name('posts.update');
    Route::delete('/articles/{post}', [PostAdminController::class, 'destroy'])->name('posts.destroy');

    // Produits
    Route::get('/produits', [ProductAdminController::class, 'index'])->name('products.index');
    Route::get('/produits/creer', [ProductAdminController::class, 'create'])->name('products.create');
    Route::post('/produits', [ProductAdminController::class, 'store'])->name('products.store');
    Route::get('/produits/{product}/modifier', [ProductAdminController::class, 'edit'])->name('products.edit');
    Route::put('/produits/{product}', [ProductAdminController::class, 'update'])->name('products.update');
    Route::patch('/produits/{product}', [ProductAdminController::class, 'togglePublished'])->name('products.toggle');
    Route::delete('/produits/{product}', [ProductAdminController::class, 'destroy'])->name('products.destroy');

    // Commandes
    Route::get('/commandes', [OrderAdminController::class, 'index'])->name('orders.index');
    Route::get('/commandes/{order}', [OrderAdminController::class, 'show'])->name('orders.show');
    Route::patch('/commandes/{order}/statut', [OrderAdminController::class, 'updateStatus'])->name('orders.status');

    // Devis
    Route::get('/devis', [QuoteAdminController::class, 'index'])->name('quotes.index');
    Route::get('/devis/{quote}', [QuoteAdminController::class, 'show'])->name('quotes.show');
    Route::patch('/devis/{quote}/statut', [QuoteAdminController::class, 'updateStatus'])->name('quotes.status');
});
