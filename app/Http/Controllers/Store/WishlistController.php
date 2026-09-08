<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $wishlistIds = session('wishlist', []);

        $products = Product::query()
            ->with('category')
            ->withSum('variants', 'stock')
            ->whereIn('id', $wishlistIds)
            ->where('is_active', true)
            ->whereHas(
                'category',
                fn ($query) => $query->where('is_active', true)
            )
            ->latest()
            ->get();

        return view('store.wishlist.index', compact('products'));
    }

    public function toggle(Product $product): RedirectResponse
    {
        abort_unless(
            $product->is_active && $product->category->is_active,
            404
        );

        $wishlist = session('wishlist', []);

        if (in_array($product->id, $wishlist, true)) {
            $wishlist = array_values(
                array_filter(
                    $wishlist,
                    fn ($id) => $id !== $product->id
                )
            );

            $message = 'Product removed from your wishlist.';
        } else {
            $wishlist[] = $product->id;
            $wishlist = array_values(array_unique($wishlist));

            $message = 'Product added to your wishlist.';
        }

        session()->put('wishlist', $wishlist);

        return back()->with('success', $message);
    }

    public function destroy(Product $product): RedirectResponse
    {
        $wishlist = session('wishlist', []);

        $wishlist = array_values(
            array_filter(
                $wishlist,
                fn ($id) => $id !== $product->id
            )
        );

        session()->put('wishlist', $wishlist);

        return back()->with(
            'success',
            'Product removed from your wishlist.'
        );
    }

    public function clear(): RedirectResponse
    {
        session()->forget('wishlist');

        return redirect()
            ->route('wishlist.index')
            ->with('success', 'Your wishlist has been cleared.');
    }
}