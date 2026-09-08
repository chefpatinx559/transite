<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Liste paginée des produits publiés avec filtres.
     */
    public function index(Request $request): Response
    {
        $query = Product::published()->with('category');

        // Recherche textuelle
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par catégorie (slug)
        if ($categorie = $request->input('categorie')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorie));
        }

        // Tri
        switch ($request->input('sort', 'recent')) {
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderByDesc('price');
                break;
            case 'popular':
                $query->orderByDesc('sales_count');
                break;
            default: // recent
                $query->orderByDesc('created_at');
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('type', 'produit')->orderBy('name')->get();

        return Inertia::render('Boutique/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'categorie', 'sort']),
        ]);
    }

    /**
     * Détail d'un produit et produits similaires.
     */
    public function show(string $slug): Response
    {
        $product = Product::published()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Product::published()
            ->with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return Inertia::render('Boutique/Show', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
