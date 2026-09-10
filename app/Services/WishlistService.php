<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class WishlistService
{
    public function ids(): array
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            return $user->wishlistProducts()
                ->pluck('products.id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return array_map(
            'intval',
            session('wishlist', [])
        );
    }

    public function products(): Collection
    {
        return Product::query()
            ->with('category')
            ->withSum('variants', 'stock')
            ->whereIn('id', $this->ids())
            ->where('is_active', true)
            ->whereHas(
                'category',
                fn ($query) =>
                    $query->where('is_active', true)
            )
            ->latest()
            ->get();
    }

    public function isFavorite(Product $product): bool
    {
        return in_array(
            $product->id,
            $this->ids(),
            true
        );
    }

    public function toggle(Product $product): bool
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $result = $user->wishlistProducts()
                ->toggle($product->id);

            return ! empty($result['attached']);
        }

        $wishlist = $this->ids();

        if (in_array($product->id, $wishlist, true)) {
            $wishlist = array_values(
                array_filter(
                    $wishlist,
                    fn ($id) => $id !== $product->id
                )
            );

            session()->put('wishlist', $wishlist);

            return false;
        }

        $wishlist[] = $product->id;

        session()->put(
            'wishlist',
            array_values(array_unique($wishlist))
        );

        return true;
    }

    public function remove(Product $product): void
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $user->wishlistProducts()
                ->detach($product->id);

            return;
        }

        $wishlist = array_values(
            array_filter(
                $this->ids(),
                fn ($id) => $id !== $product->id
            )
        );

        session()->put('wishlist', $wishlist);
    }

    public function clear(): void
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $user->wishlistProducts()->detach();

            return;
        }

        session()->forget('wishlist');
    }

    public function count(): int
    {
        return count($this->ids());
    }

    public function mergeGuestWishlist(User $user): void
    {
        $guestIds = array_map(
            'intval',
            session('wishlist', [])
        );

        if (empty($guestIds)) {
            return;
        }

        $validIds = Product::query()
            ->whereIn('id', $guestIds)
            ->where('is_active', true)
            ->pluck('id')
            ->all();

        $user->wishlistProducts()
            ->syncWithoutDetaching($validIds);

        session()->forget('wishlist');
    }
}