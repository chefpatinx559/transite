<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    private const SESSION_KEY = 'cart';

    public function index(): Response
    {
        [$items, $subtotal] = $this->buildCartData();

        return Inertia::render('Panier', [
            'items'    => $items,
            'subtotal' => $subtotal,
            'total'    => $subtotal,   // shipping_cost = 0 pour l'instant
            'shipping' => 0,
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'sometimes|integer|min:1|max:100',
        ]);

        $productId = (int) $request->input('product_id');
        $quantity  = (int) $request->input('quantity', 1);

        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $product    = Product::findOrFail($productId);
            $firstImage = $product->images[0] ?? null;
            $imageUrl   = is_array($firstImage) ? ($firstImage['url'] ?? null) : $firstImage;

            $cart[$productId] = [
                'product_id' => $productId,
                'name'       => $product->name,
                'price'      => (float) $product->price,
                'quantity'   => $quantity,
                'image'      => $imageUrl,
                'slug'       => $product->slug,
            ];
        }

        $this->saveCart($cart);

        return redirect()->back()->with('success', 'Produit ajouté au panier.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:100']);

        $productId = (int) $id;
        $cart      = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = (int) $request->input('quantity');
            $this->saveCart($cart);
        }

        return redirect()->back()->with('success', 'Panier mis à jour.');
    }

    public function remove(string $id): RedirectResponse
    {
        $cart = $this->getCart();
        unset($cart[(int) $id]);
        $this->saveCart($cart);

        return redirect()->back()->with('success', 'Article retiré du panier.');
    }

    public function clear(): RedirectResponse
    {
        $this->saveCart([]);

        return redirect()->back()->with('success', 'Panier vidé.');
    }

    // ── Private Helpers ───────────────────────────────────────────────────

    private function getCart(): array
    {
        return session(self::SESSION_KEY, []);
    }

    private function saveCart(array $cart): void
    {
        session([self::SESSION_KEY => $cart]);
    }

    /**
     * Recharge les prix et images depuis la BDD pour garantir la fraîcheur,
     * puis calcule les sous-totaux.
     */
    private function buildCartData(): array
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return [[], 0.0];
        }

        // Recharge les produits pour obtenir prix et images à jour
        $productIds = array_keys($cart);
        $products   = Product::whereIn('id', $productIds)
            ->get(['id', 'name', 'price', 'images', 'slug'])
            ->keyBy('id');

        $subtotal = 0.0;
        $items    = [];
        $updated  = $cart;

        foreach ($cart as $productId => $entry) {
            $product = $products->get($productId);

            if (! $product) {
                // Produit supprimé — on le retire du panier
                unset($updated[$productId]);
                continue;
            }

            // Rafraîchir prix et image
            $freshPrice  = (float) $product->price;
            $firstImage  = $product->images[0] ?? null;
            $freshImage  = is_array($firstImage) ? ($firstImage['url'] ?? null) : $firstImage;

            $updated[$productId]['price'] = $freshPrice;
            $updated[$productId]['image'] = $freshImage;
            $updated[$productId]['name']  = $product->name;

            $qty     = (int) $entry['quantity'];
            $lineSub = $freshPrice * $qty;
            $subtotal += $lineSub;

            $items[] = array_merge($updated[$productId], [
                'quantity' => $qty,
                'subtotal' => $lineSub,
            ]);
        }

        // Sauvegarder les données fraîches en session
        $this->saveCart($updated);

        return [$items, $subtotal];
    }
}
