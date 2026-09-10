<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceOrderRequest;
use App\Mail\OrderConfirmed;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function index(
        CartService $cartService
    ): View|RedirectResponse {
        $cart = $cartService->items();

        if ($cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        [$items, $error] = $this->prepareCartItems($cart);

        if ($error) {
            return redirect()
                ->route('cart.index')
                ->with('error', $error);
        }

        $subtotal = $items->sum('subtotal');
        $deliveryFee = 3;
        $total = $subtotal + $deliveryFee;

        return view('store.checkout.index', compact(
            'items',
            'subtotal',
            'deliveryFee',
            'total'
        ));
    }

    public function store(
        PlaceOrderRequest $request,
        CartService $cartService
    ): RedirectResponse {
        $cart = $cartService->items();

        if ($cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        try {
            $order = DB::transaction(
                function () use ($request, $cart): Order {
                    $variantIds = $cart
                        ->pluck('variant_id')
                        ->all();

                    $variants = ProductVariant::query()
                        ->with('product')
                        ->whereIn('id', $variantIds)
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    $orderItems = collect();
                    $subtotal = 0;

                    foreach ($cart as $cartItem) {
                        $variant = $variants->get(
                            $cartItem['variant_id']
                        );

                        if (
                            ! $variant
                            || ! $variant->product
                            || ! $variant->product->is_active
                        ) {
                            throw new \RuntimeException(
                                "{$cartItem['name']} is no longer available."
                            );
                        }

                        $quantity = (int) $cartItem['quantity'];

                        if (
                            $quantity < 1
                            || $quantity > $variant->stock
                        ) {
                            throw new \RuntimeException(
                                'The requested quantity for ' .
                                "{$variant->product->name} is unavailable."
                            );
                        }

                        $unitPrice =
                            (float) $variant->product->price;

                        $itemSubtotal =
                            $unitPrice * $quantity;

                        $subtotal += $itemSubtotal;

                        $orderItems->push([
                            'variant' => $variant,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'subtotal' => $itemSubtotal,
                        ]);
                    }

                    $deliveryFee = 3;
                    $total = $subtotal + $deliveryFee;

                    $order = Order::create([
                        'user_id' => $request->user()->id,

                        'order_number' =>
                            $this->generateOrderNumber(),

                        'customer_name' =>
                            $request->validated(
                                'customer_name'
                            ),

                        'phone' =>
                            $request->validated('phone'),

                        'city' =>
                            $request->validated('city'),

                        'address' =>
                            $request->validated('address'),

                        'total_amount' => $total,
                        'status' => 'pending',

                        'payment_method' =>
                            'cash_on_delivery',

                        'notes' =>
                            $request->validated('notes'),
                    ]);

                    foreach ($orderItems as $item) {
                        $variant = $item['variant'];

                        $order->items()->create([
                            'product_id' =>
                                $variant->product->id,

                            'product_variant_id' =>
                                $variant->id,

                            'product_name' =>
                                $variant->product->name,

                            'size' => $variant->size,
                            'color' => $variant->color,

                            'unit_price' =>
                                $item['unit_price'],

                            'quantity' =>
                                $item['quantity'],

                            'subtotal' =>
                                $item['subtotal'],
                        ]);

                        $variant->decrement(
                            'stock',
                            $item['quantity']
                        );
                    }

                    return $order;
                },
                attempts: 3
            );
        } catch (Throwable $exception) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }

        $order->load('items');

        try {
            Mail::to($request->user()->email)
                ->queue(new OrderConfirmed($order));
        } catch (Throwable $exception) {
            report($exception);
        }

        $cartService->clear();

        return redirect()
            ->route('checkout.success', $order)
            ->with(
                'success',
                'Your order was placed successfully.'
            );
    }

    public function success(Order $order): View
    {
        abort_unless(
            $order->user_id === Auth::id(),
            403
        );

        $order->load('items');

        return view(
            'store.checkout.success',
            compact('order')
        );
    }

    private function prepareCartItems(
        array|Collection $cart
    ): array {
        $variantIds = collect($cart)
            ->pluck('variant_id')
            ->all();

        $variants = ProductVariant::query()
            ->with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $items = collect();

        foreach ($cart as $cartItem) {
            $variant = $variants->get(
                $cartItem['variant_id']
            );

            if (
                ! $variant
                || ! $variant->product
                || ! $variant->product->is_active
            ) {
                return [
                    collect(),
                    "{$cartItem['name']} is no longer available.",
                ];
            }

            $quantity = (int) $cartItem['quantity'];

            if ($quantity < 1) {
                return [
                    collect(),
                    "The quantity for " .
                    "{$variant->product->name} is invalid.",
                ];
            }

            if ($quantity > $variant->stock) {
                return [
                    collect(),
                    "Only {$variant->stock} of " .
                    "{$variant->product->name} are available.",
                ];
            }

            $unitPrice =
                (float) $variant->product->price;

            $items->push([
                'variant' => $variant,
                'product' => $variant->product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $unitPrice * $quantity,
            ]);
        }

        return [$items, null];
    }

    private function generateOrderNumber(): string
    {
        do {
            $number =
                'SH-' .
                now()->format('Ymd') .
                '-' .
                Str::upper(Str::random(6));
        } while (
            Order::query()
                ->where('order_number', $number)
                ->exists()
        );

        return $number;
    }
}