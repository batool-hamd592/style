@extends('layouts.store')

@section('title', 'Shopping Cart | StyleHub')

@section('content')
    <section class="bg-gray-950 py-14 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.25em]
                      text-rose-400">
                Your selections
            </p>

            <h1 class="mt-3 text-4xl font-black">
                Shopping Cart
            </h1>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-6 rounded-xl bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-xl bg-red-100 p-4 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($cart->isEmpty())
            <div class="rounded-3xl bg-white px-6 py-20 text-center shadow-sm">
                <div class="text-6xl">🛒</div>

                <h2 class="mt-6 text-2xl font-bold">
                    Your cart is empty
                </h2>

                <p class="mt-2 text-gray-500">
                    Discover our latest collection and add something you love.
                </p>

                <a
                    href="{{ route('store.products.index') }}"
                    class="mt-7 inline-flex rounded-full bg-gray-950
                           px-7 py-3 font-semibold text-white
                           hover:bg-rose-600"
                >
                    Continue Shopping
                </a>
            </div>
        @else
            <div class="grid gap-8 lg:grid-cols-3">
                <div class="space-y-4 lg:col-span-2">
                    @foreach ($cart as $item)
                        <article class="flex flex-col gap-5 rounded-2xl
                                        bg-white p-5 shadow-sm sm:flex-row">
                            <a
                                href="{{ route(
                                    'store.products.show',
                                    $item['slug']
                                ) }}"
                                class="h-36 w-full overflow-hidden
                                       rounded-xl bg-gray-100 sm:w-28"
                            >
                                @if ($item['image'])
                                    <img
                                        src="{{ Storage::url($item['image']) }}"
                                        alt="{{ $item['name'] }}"
                                        class="h-full w-full object-cover"
                                    >
                                @else
                                    <div class="flex h-full items-center
                                                justify-center text-gray-400">
                                        No image
                                    </div>
                                @endif
                            </a>

                            <div class="flex flex-1 flex-col justify-between">
                                <div>
                                    <div class="flex justify-between gap-4">
                                        <div>
                                            <a
                                                href="{{ route(
                                                    'store.products.show',
                                                    $item['slug']
                                                ) }}"
                                                class="text-lg font-bold
                                                       hover:text-rose-600"
                                            >
                                                {{ $item['name'] }}
                                            </a>

                                            <p class="mt-1 text-sm text-gray-500">
                                                Size: {{ $item['size'] }}
                                                · Color: {{ $item['color'] }}
                                            </p>
                                        </div>

                                        <p class="font-bold">
                                            {{ number_format(
                                                $item['price']
                                                * $item['quantity'],
                                                2
                                            ) }} JOD
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-5 flex flex-wrap
                                            items-center justify-between gap-4">
                                    <form
                                        action="{{ route(
                                            'cart.update',
                                            $item['variant_id']
                                        ) }}"
                                        method="POST"
                                        class="flex items-center gap-3"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <label class="text-sm text-gray-600">
                                            Quantity
                                        </label>

                                        <input
                                            name="quantity"
                                            type="number"
                                            min="1"
                                            max="{{ min(10, $item['stock']) }}"
                                            value="{{ $item['quantity'] }}"
                                            class="w-20 rounded-lg border-gray-300"
                                        >

                                        <button
                                            type="submit"
                                            class="text-sm font-semibold
                                                   text-blue-600 hover:text-blue-800"
                                        >
                                            Update
                                        </button>
                                    </form>

                                    <form
                                        action="{{ route(
                                            'cart.destroy',
                                            $item['variant_id']
                                        ) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-sm font-semibold
                                                   text-red-600 hover:text-red-800"
                                        >
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="h-fit rounded-2xl bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-bold">Order Summary</h2>

                    <div class="mt-6 space-y-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Items</span>

                            <span>
                                {{ $cart->sum('quantity') }}
                            </span>
                        </div>

                        <div class="flex justify-between text-gray-600">
                            <span>Delivery</span>
                            <span>Calculated at checkout</span>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between text-xl font-bold">
                                <span>Total</span>

                                <span>
                                    {{ number_format($total, 2) }} JOD
                                </span>
                            </div>
                        </div>
                    </div>

                    @auth
                        <a
                            href="#"
                            class="mt-6 block rounded-full bg-gray-950
                                   px-6 py-3 text-center font-semibold
                                   text-white hover:bg-rose-600"
                        >
                            Continue to Checkout
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="mt-6 block rounded-full bg-gray-950
                                   px-6 py-3 text-center font-semibold
                                   text-white hover:bg-rose-600"
                        >
                            Login to Checkout
                        </a>

                        <p class="mt-3 text-center text-xs text-gray-500">
                            You only need an account when confirming the order.
                        </p>
                    @endauth

                    <form
                        action="{{ route('cart.clear') }}"
                        method="POST"
                        class="mt-4"
                        onsubmit="return confirm('Clear your cart?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="w-full text-sm font-semibold
                                   text-red-600 hover:text-red-800"
                        >
                            Clear Cart
                        </button>
                    </form>
                </aside>
            </div>
        @endif
    </section>
@endsection