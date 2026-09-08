@extends('layouts.store')

@section('title', 'Wishlist | StyleHub')

@section('content')
    <section class="bg-gray-950 py-14 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.25em]
                      text-rose-400">
                Saved for later
            </p>

            <h1 class="mt-3 text-4xl font-black">
                My Wishlist
            </h1>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-6 rounded-xl bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($products->isEmpty())
            <div class="rounded-3xl bg-white px-6 py-20
                        text-center shadow-sm">
                <div class="text-6xl">♡</div>

                <h2 class="mt-6 text-2xl font-bold">
                    Your wishlist is empty
                </h2>

                <p class="mt-2 text-gray-500">
                    Save the products you love and return to them later.
                </p>

                <a
                    href="{{ route('store.products.index') }}"
                    class="mt-7 inline-flex rounded-full bg-gray-950
                           px-7 py-3 font-semibold text-white
                           hover:bg-rose-600"
                >
                    Explore Products
                </a>
            </div>
        @else
            <div class="mb-7 flex items-center justify-between">
                <p class="text-gray-600">
                    {{ $products->count() }} saved products
                </p>

                <form
                    action="{{ route('wishlist.clear') }}"
                    method="POST"
                    onsubmit="return confirm('Clear your wishlist?')"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="font-semibold text-red-600
                               hover:text-red-800"
                    >
                        Clear Wishlist
                    </button>
                </form>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($products as $product)
                    <div>
                        <x-product-card :product="$product"/>

                        <form
                            action="{{ route(
                                'wishlist.destroy',
                                $product
                            ) }}"
                            method="POST"
                            class="mt-3"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="w-full rounded-full border
                                       border-red-200 px-5 py-2
                                       text-sm font-semibold text-red-600
                                       hover:bg-red-50"
                            >
                                Remove
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection