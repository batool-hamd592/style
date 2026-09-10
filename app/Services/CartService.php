<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function items(): Collection
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $cart = $user->cart()
                ->with('items.variant.product')
                ->first();

            if (! $cart) {
                return collect();
            }

            return $cart->items
                ->filter(
                    fn ($item) =>
                        $item->variant
                        && $item->variant->product
                        && $item->variant->product->is_active
                )
                ->mapWithKeys(function ($item) {
                    $variant = $item->variant;
                    $product = $variant->product;

                    return [
                        (string) $variant->id => [
                            'variant_id' => $variant->id,
                            'product_id' => $product->id,
                            'name' => $product->name,
                            'slug' => $product->slug,
                            'image' => $product->image,
                            'size' => $variant->size,
                            'color' => $variant->color,
                            'price' => (float) $product->price,
                            'quantity' => (int) $item->quantity,
                            'stock' => (int) $variant->stock,
                        ],
                    ];
                });
        }

        return collect(session('cart', []));
    }

    public function currentQuantity(
        ProductVariant $variant
    ): int {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $cart = $user->cart()->first();

            if (! $cart) {
                return 0;
            }

            return (int) (
                $cart->items()
                    ->where(
                        'product_variant_id',
                        $variant->id
                    )
                    ->value('quantity') ?? 0
            );
        }

        return (int) session(
            "cart.{$variant->id}.quantity",
            0
        );
    }

    public function add(
        ProductVariant $variant,
        int $quantity
    ): void {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $cart = Cart::firstOrCreate([
                'user_id' => $user->id,
            ]);

            $item = $cart->items()->firstOrNew([
                'product_variant_id' => $variant->id,
            ]);

            $item->quantity =
                (int) ($item->quantity ?? 0) + $quantity;

            $item->save();

            return;
        }

        $cart = session('cart', []);
        $key = (string) $variant->id;

        $currentQuantity = (int) (
            $cart[$key]['quantity'] ?? 0
        );

        $cart[$key] = $this->sessionItem(
            $variant,
            $currentQuantity + $quantity
        );

        session()->put('cart', $cart);
    }

    public function update(
        ProductVariant $currentVariant,
        ProductVariant $newVariant,
        int $quantity
    ): bool {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            return DB::transaction(
                function () use (
                    $user,
                    $currentVariant,
                    $newVariant,
                    $quantity
                ): bool {
                    $cart = $user->cart()->first();

                    if (! $cart) {
                        return false;
                    }

                    $currentItem = $cart->items()
                        ->where(
                            'product_variant_id',
                            $currentVariant->id
                        )
                        ->lockForUpdate()
                        ->first();

                    if (! $currentItem) {
                        return false;
                    }

                    if (
                        $currentVariant->id
                        === $newVariant->id
                    ) {
                        $currentItem->update([
                            'quantity' => $quantity,
                        ]);

                        return true;
                    }

                    $newItem = $cart->items()
                        ->where(
                            'product_variant_id',
                            $newVariant->id
                        )
                        ->lockForUpdate()
                        ->first();

                    if ($newItem) {
                        $newItem->update([
                            'quantity' =>
                                $newItem->quantity + $quantity,
                        ]);
                    } else {
                        $cart->items()->create([
                            'product_variant_id' =>
                                $newVariant->id,

                            'quantity' => $quantity,
                        ]);
                    }

                    $currentItem->delete();

                    return true;
                }
            );
        }

        $cart = session('cart', []);

        $currentKey = (string) $currentVariant->id;
        $newKey = (string) $newVariant->id;

        if (! isset($cart[$currentKey])) {
            return false;
        }

        if ($currentKey === $newKey) {
            $cart[$currentKey] = $this->sessionItem(
                $newVariant,
                $quantity
            );

            session()->put('cart', $cart);

            return true;
        }

        $existingQuantity = (int) (
            $cart[$newKey]['quantity'] ?? 0
        );

        unset($cart[$currentKey]);

        $cart[$newKey] = $this->sessionItem(
            $newVariant,
            $existingQuantity + $quantity
        );

        session()->put('cart', $cart);

        return true;
    }

    public function remove(
        ProductVariant $variant
    ): void {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $user->cart?->items()
                ->where(
                    'product_variant_id',
                    $variant->id
                )
                ->delete();

            return;
        }

        $cart = session('cart', []);

        unset($cart[(string) $variant->id]);

        session()->put('cart', $cart);
    }

    public function clear(): void
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $user->cart?->items()->delete();

            return;
        }

        session()->forget('cart');
    }

    public function count(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function total(): float
    {
        return (float) $this->items()->sum(
            fn (array $item) =>
                $item['price'] * $item['quantity']
        );
    }

    public function mergeGuestCart(
        User $user
    ): void {
        $guestCart = session('cart', []);

        if (empty($guestCart)) {
            return;
        }

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
        ]);

        foreach ($guestCart as $guestItem) {
            $variant = ProductVariant::query()
                ->with('product')
                ->find($guestItem['variant_id']);

            if (
                ! $variant
                || ! $variant->product
                || ! $variant->product->is_active
                || $variant->stock < 1
            ) {
                continue;
            }

            $item = $cart->items()->firstOrNew([
                'product_variant_id' => $variant->id,
            ]);

            $mergedQuantity = min(
                (int) ($item->quantity ?? 0)
                    + (int) $guestItem['quantity'],
                (int) $variant->stock,
                10
            );

            $item->quantity = $mergedQuantity;
            $item->save();
        }

        session()->forget('cart');
    }

    private function sessionItem(
        ProductVariant $variant,
        int $quantity
    ): array {
        $variant->loadMissing('product');

        $product = $variant->product;

        return [
            'variant_id' => $variant->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'image' => $product->image,
            'size' => $variant->size,
            'color' => $variant->color,
            'price' => (float) $product->price,
            'quantity' => $quantity,
            'stock' => (int) $variant->stock,
        ];
    }
}