<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(
        private readonly WishlistService $wishlistService
    ) {
    }

    public function index(): View
    {
        $products = $this->wishlistService->products();

        return view(
            'store.wishlist.index',
            compact('products')
        );
    }

    public function toggle(
        Request $request,
        Product $product
    ): JsonResponse|RedirectResponse {
        abort_unless(
            $product->is_active
            && $product->category
            && $product->category->is_active,
            404
        );

        $added = $this->wishlistService->toggle($product);

        $message = $added
            ? 'Product added to your wishlist.'
            : 'Product removed from your wishlist.';

        if ($request->expectsJson()) {
            return response()->json([
                'added' => $added,
                'count' => $this->wishlistService->count(),
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(
        Product $product
    ): RedirectResponse {
        $this->wishlistService->remove($product);

        return back()->with(
            'success',
            'Product removed from your wishlist.'
        );
    }

    public function clear(): RedirectResponse
    {
        $this->wishlistService->clear();

        return redirect()
            ->route('wishlist.index')
            ->with(
                'success',
                'Your wishlist has been cleared.'
            );
    }
}