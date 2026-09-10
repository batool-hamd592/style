<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {
    }

    public function index(): View
    {
        $cart = $this->cartService->items();
        $total = $this->cartService->total();

        $productIds = $cart
            ->pluck('product_id')
            ->unique()
            ->values();

        $variantsByProduct = ProductVariant::query()
            ->whereIn('product_id', $productIds)
            ->orderBy('color')
            ->orderBy('size')
            ->get()
            ->groupBy('product_id');

        return view(
            'store.cart.index',
            compact(
                'cart',
                'total',
                'variantsByProduct'
            )
        );
    }

    public function store(
        AddToCartRequest $request
    ): RedirectResponse {
        $variant = ProductVariant::query()
            ->with('product')
            ->findOrFail(
                $request->validated('variant_id')
            );

        if (! $variant->product?->is_active) {
            return back()->with(
                'error',
                'This product is currently unavailable.'
            );
        }

        $quantity = $request->integer('quantity');

        $newQuantity =
            $this->cartService->currentQuantity($variant)
            + $quantity;

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

        $this->cartService->add(
            $variant,
            $quantity
        );

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Product added to your cart.'
            );
    }

    public function update(
        UpdateCartRequest $request,
        ProductVariant $variant
    ): RedirectResponse {
        $newVariant = ProductVariant::query()
            ->with('product')
            ->findOrFail(
                $request->validated('variant_id')
            );

        if (
            $newVariant->product_id
            !== $variant->product_id
        ) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'The selected option does not belong '
                    . 'to this product.'
                );
        }

        if (! $newVariant->product?->is_active) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'This product is currently unavailable.'
                );
        }

        $quantity = $request->integer('quantity');

        $existingQuantity = 0;

        if ($newVariant->id !== $variant->id) {
            $existingQuantity =
                $this->cartService
                    ->currentQuantity($newVariant);
        }

        $finalQuantity =
            $existingQuantity + $quantity;

        if ($finalQuantity > $newVariant->stock) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    "Only {$newVariant->stock} items "
                    . 'of the selected option are available.'
                );
        }

        if ($finalQuantity > 10) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'The maximum quantity per option is 10.'
                );
        }

        $updated = $this->cartService->update(
            $variant,
            $newVariant,
            $quantity
        );

        if (! $updated) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Cart item was not found.'
                );
        }

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Cart item updated successfully.'
            );
    }

    public function destroy(
        ProductVariant $variant
    ): RedirectResponse {
        $this->cartService->remove($variant);

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Product removed from your cart.'
            );
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Your cart has been cleared.'
            );
    }
}