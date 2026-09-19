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
            'name'                 => 'required|string|max:255',
            'category_id'          => 'nullable|exists:categories,id',
            'description'          => 'nullable|string',
            'price'                => 'required|numeric|min:0',
            'compare_price'        => 'nullable|numeric',
            'stock'                => 'required|integer|min:-1',
            'type'                 => 'required|in:physical,digital,course',
            'sku'                  => 'nullable|string|max:100',
            'weight'               => 'nullable|numeric',
            'is_published'         => 'boolean',
            'is_featured'          => 'boolean',
            'photos'               => 'nullable|array|max:10',
            'photos.*'             => 'image|mimes:jpeg,png,webp|max:5120',
            'shipping_air_express' => 'nullable|numeric|min:0',
            'shipping_air_normal'  => 'nullable|numeric|min:0',
            'shipping_sea'         => 'nullable|numeric|min:0',
        ]);

        $base = Str::slug($validated['name']);
        $slug = $base;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        $validated['slug'] = $slug;

        if ($request->hasFile('photos')) {
            $images = [];
            foreach ($request->file('photos') as $file) {
                $path = $file->store('products', 'public');
                $images[] = ['url' => '/storage/' . $path, 'alt' => $validated['name']];
            }
            $validated['images'] = $images;
        }
        unset($validated['photos']);

        $product = Product::create($validated);

        return response()->json($product->load('category'), 201);
    }

    /**
     * Met à jour un produit (accepte POST avec _method=PUT pour FormData).
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'category_id'          => 'nullable|exists:categories,id',
            'description'          => 'nullable|string',
            'price'                => 'required|numeric|min:0',
            'compare_price'        => 'nullable|numeric',
            'stock'                => 'required|integer|min:-1',
            'type'                 => 'required|in:physical,digital,course',
            'sku'                  => 'nullable|string|max:100',
            'weight'               => 'nullable|numeric',
            'is_published'         => 'boolean',
            'is_featured'          => 'boolean',
            'existing_images'      => 'nullable|string',
            'photos'               => 'nullable|array|max:10',
            'photos.*'             => 'image|mimes:jpeg,png,webp|max:5120',
            'shipping_air_express' => 'nullable|numeric|min:0',
            'shipping_air_normal'  => 'nullable|numeric|min:0',
            'shipping_sea'         => 'nullable|numeric|min:0',
        ]);

        // Images existantes à conserver
        $existingImages = [];
        if ($request->filled('existing_images')) {
            $decoded = json_decode($request->input('existing_images'), true);
            if (is_array($decoded)) $existingImages = $decoded;
        }
        // Nouvelles photos uploadées
        $newImages = [];
        if ($request->hasFile('photos')) {
            // Supprimer anciennes photos non conservées
            $keptUrls = array_column($existingImages, 'url');
            $oldImages = $product->images ?? [];
            foreach ($oldImages as $old) {
                $oldUrl = is_array($old) ? ($old['url'] ?? '') : $old;
                if (!in_array($oldUrl, $keptUrls)) {
                    $oldPath = str_replace('/storage/', '', $oldUrl);
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }
            foreach ($request->file('photos') as $file) {
                $path = $file->store('products', 'public');
                $newImages[] = ['url' => '/storage/' . $path, 'alt' => $validated['name']];
            }
        }
        $allImages = array_merge($existingImages, $newImages);
        if (!empty($allImages) || $request->hasFile('photos') || $request->filled('existing_images')) {
            $validated['images'] = $allImages ?: null;
        }
        unset($validated['photos'], $validated['existing_images']);

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
