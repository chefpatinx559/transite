<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');

        // Pages statiques
        $staticPages = [
            ['url' => '/',             'priority' => '1.0',  'changefreq' => 'weekly'],
            ['url' => '/boutique',     'priority' => '0.9',  'changefreq' => 'daily'],
            ['url' => '/formations',   'priority' => '0.9',  'changefreq' => 'weekly'],
            ['url' => '/blog',         'priority' => '0.8',  'changefreq' => 'daily'],
            ['url' => '/services',     'priority' => '0.8',  'changefreq' => 'monthly'],
            ['url' => '/importer',     'priority' => '0.8',  'changefreq' => 'monthly'],
            ['url' => '/a-propos',     'priority' => '0.6',  'changefreq' => 'monthly'],
            ['url' => '/contact',      'priority' => '0.6',  'changefreq' => 'monthly'],
            ['url' => '/faq',          'priority' => '0.5',  'changefreq' => 'monthly'],
        ];

        // Articles de blog publiés
        $posts = Post::published()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at']);

        // Produits publiés
        $products = Product::where('is_published', true)
            ->orderByDesc('created_at')
            ->get(['slug', 'updated_at']);

        // Formations publiées
        $formations = Formation::published()
            ->orderBy('date_start')
            ->get(['slug', 'updated_at']);

        $xml = view('sitemap', compact('baseUrl', 'staticPages', 'posts', 'products', 'formations'));

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
