<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductAdminController extends Controller
{
    /**
     * Liste paginée des produits.
     */
    public function index(): Response
    {
        $products = Product::with('category')
            ->orderByDesc('created_at')
            ->paginate(15);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Formulaire de création.
     */
    public function create(): Response
    {
        $categories = Category::where('type', 'produit')->orderBy('name')->get();

        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Enregistre un nouveau produit.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'   => 'nullable|integer|exists:categories,id',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'content'       => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'sku'           => 'nullable|string|max:100|unique:products,sku',
            'stock'         => 'required|integer|min:-1',
            'type'          => 'required|in:physical,digital,course',
            'weight'        => 'nullable|numeric|min:0',
            'is_published'  => 'boolean',
            'is_featured'   => 'boolean',
            'photo'         => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        $validated['slug'] = $this->uniqueSlug(Str::slug($validated['name']));

        // Upload photo principale
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products', 'public');
            $validated['images'] = [['url' => '/storage/' . $path, 'alt' => $validated['name']]];
        }

        unset($validated['photo']);
        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Product $product): Response
    {
        $categories = Category::where('type', 'produit')->orderBy('name')->get();

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product->load('category'),
            'categories' => $categories,
        ]);
    }

    /**
     * Met à jour un produit.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'   => 'nullable|integer|exists:categories,id',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'content'       => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'sku'           => 'nullable|string|max:100|unique:products,sku,'.$product->id,
            'stock'         => 'required|integer|min:-1',
            'type'          => 'required|in:physical,digital,course',
            'weight'        => 'nullable|numeric|min:0',
            'is_published'  => 'boolean',
            'is_featured'   => 'boolean',
            'photo'         => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        // Nouvelle photo uploadée
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products', 'public');
            $validated['images'] = [['url' => '/storage/' . $path, 'alt' => $validated['name']]];
        }

        unset($validated['photo']);
        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit mis à jour.');
    }

    /**
     * Bascule la visibilité (toggle publié/brouillon) via PATCH.
     */
    public function togglePublished(Request $request, Product $product): RedirectResponse
    {
        $product->update([
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->back()->with('success', 'Visibilité mise à jour.');
    }

    /**
     * Supprime un produit.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit supprimé.');
    }

    private function uniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $original = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }
        return $slug;
    }
}
