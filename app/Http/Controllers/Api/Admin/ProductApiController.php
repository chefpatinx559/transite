<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductApiController extends Controller
{
    /**
     * Liste paginée des produits avec filtres et recherche.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category')->orderByDesc('created_at');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_published')) {
            $query->where('is_published', filter_var($request->is_published, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('sort')) {
            match ($request->sort) {
                'price_asc'   => $query->reorder('price', 'asc'),
                'price_desc'  => $query->reorder('price', 'desc'),
                'name_asc'    => $query->reorder('name', 'asc'),
                'name_desc'   => $query->reorder('name', 'desc'),
                'oldest'      => $query->reorder('created_at', 'asc'),
                default       => null,
            };
        }

        return response()->json($query->paginate(15));
    }

    /**
     * Retourne un produit avec sa catégorie.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load('category'));
    }

    /**
     * Crée un nouveau produit.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'nullable|exists:categories,id',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric',
            'stock'         => 'required|integer|min:-1',
            'type'          => 'required|in:physical,digital,course',
            'sku'           => 'nullable|string|max:100',
            'weight'        => 'nullable|numeric',
            'is_published'  => 'boolean',
            'is_featured'   => 'boolean',
            'photo'         => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        $base = Str::slug($validated['name']);
        $slug = $base;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        $validated['slug'] = $slug;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products', 'public');
            $validated['images'] = [['url' => '/storage/'.$path, 'alt' => $validated['name']]];
        }

        unset($validated['photo']);

        $product = Product::create($validated);

        return response()->json($product->load('category'), 201);
    }

    /**
     * Met à jour un produit (accepte POST avec _method=PUT pour FormData).
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'nullable|exists:categories,id',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric',
            'stock'         => 'required|integer|min:-1',
            'type'          => 'required|in:physical,digital,course',
            'sku'           => 'nullable|string|max:100',
            'weight'        => 'nullable|numeric',
            'is_published'  => 'boolean',
            'is_featured'   => 'boolean',
            'photo'         => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($product->images && isset($product->images[0]['url'])) {
                $oldPath = str_replace('/storage/', '', $product->images[0]['url']);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('photo')->store('products', 'public');
            $validated['images'] = [['url' => '/storage/'.$path, 'alt' => $validated['name']]];
        }

        unset($validated['photo']);

        $product->update($validated);

        return response()->json($product->load('category'));
    }

    /**
     * Supprime un produit.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, 204);
    }

    /**
     * Bascule la visibilité du produit.
     */
    public function toggle(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $product->update(['is_published' => $request->boolean('is_published')]);

        return response()->json($product->load('category'));
    }
}
