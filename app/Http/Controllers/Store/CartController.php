<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = collect(session('cart', []));

        $total = $cart->sum(
            fn (array $item) =>
                (float) $item['price'] * (int) $item['quantity']
        );

        return view('store.cart.index', compact('cart', 'total'));
    }

    public function store(
        AddToCartRequest $request
    ): RedirectResponse {
        $variant = ProductVariant::query()
            ->with('product')
            ->findOrFail($request->validated('variant_id'));

        if (! $variant->product->is_active) {
            return back()->with(
                'error',
                'This product is currently unavailable.'
            );
        }

        if ($variant->stock < 1) {
            return back()->with(
                'error',
                'This product option is out of stock.'
            );
        }

        $cart = session('cart', []);
        $key = (string) $variant->id;

        $currentQuantity = $cart[$key]['quantity'] ?? 0;
        $requestedQuantity = $request->integer('quantity');
        $newQuantity = $currentQuantity + $requestedQuantity;

        if ($newQuantity > $variant->stock) {
            return back()->with(
                'error',
                "Only {$variant->stock} items are available."
            );
        }

        if ($newQuantity > 10) {
            return back()->with(
                'error',
                'The maximum quantity per product is 10.'
            );
        }

        $cart[$key] = [
            'variant_id' => $variant->id,
            'product_id' => $variant->product->id,
            'name' => $variant->product->name,
            'slug' => $variant->product->slug,
            'image' => $variant->product->image,
            'size' => $variant->size,
            'color' => $variant->color,
            'price' => (float) $variant->product->price,
            'quantity' => $newQuantity,
            'stock' => $variant->stock,
        ];

        session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Product added to your cart.');
    }

    public function update(
        UpdateCartRequest $request,
        ProductVariant $variant
    ): RedirectResponse {
        $cart = session('cart', []);
        $key = (string) $variant->id;

        if (! isset($cart[$key])) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Cart item was not found.');
        }

        $quantity = $request->integer('quantity');

        if ($quantity > $variant->stock) {
            return back()->with(
                'error',
                "Only {$variant->stock} items are available."
            );
        }

        $cart[$key]['quantity'] = $quantity;
        $cart[$key]['stock'] = $variant->stock;

        session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Cart quantity updated.');
    }

    public function destroy(
        ProductVariant $variant
    ): RedirectResponse {
        $cart = session('cart', []);
        $key = (string) $variant->id;

        unset($cart[$key]);

        session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Product removed from your cart.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()
            ->route('cart.index')
            ->with('success', 'Your cart has been cleared.');
    }
}