@extends('layouts.store')

@section('title', 'StyleHub | Fashion for Everyone')

@section('content')
    <section class="relative overflow-hidden bg-gray-950 text-white">
        <div class="mx-auto grid min-h-[560px] max-w-7xl items-center
                    gap-10 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div class="relative z-10">
                <p class="text-sm font-semibold uppercase tracking-[0.3em]
                          text-rose-400">
                    New Collection
                </p>

                <h1 class="mt-5 max-w-xl text-5xl font-black leading-tight
                           sm:text-6xl">
                    Style made for every moment.
                </h1>

                <p class="mt-6 max-w-lg text-lg text-gray-300">
                    Discover modern fashion for women, men and kids.
                </p>

                <a
                    href="{{ route('store.products.index') }}"
                    class="mt-8 inline-flex rounded-full bg-rose-600
                           px-7 py-3 font-semibold text-white
                           hover:bg-rose-500"
                >
                    Shop Collection
                </a>
            </div>

            <div class="relative hidden lg:block">
                <div class="absolute -right-20 -top-32 h-96 w-96
                            rounded-full bg-rose-600/30 blur-3xl"></div>

                <div class="relative rounded-[3rem] border border-white/10
                            bg-gradient-to-br from-rose-500/30
                            to-purple-500/20 p-16 text-center">
                    <p class="text-8xl">🛍️</p>
                    <p class="mt-5 text-2xl font-bold">
                        Women · Men · Kids
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider
                          text-rose-600">
                    Collections
                </p>

                <h2 class="mt-2 text-3xl font-black">
                    Shop by category
                </h2>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($categories as $category)
                <a
                    href="{{ route('store.products.index', [
                        'category' => $category->slug
                    ]) }}"
                    class="rounded-2xl bg-white p-6 shadow-sm
                           transition hover:-translate-y-1 hover:shadow-md"
                >
                    <h3 class="text-xl font-bold">
                        {{ $category->name }}
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        {{ $category->products_count }} products
                    </p>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider
                          text-rose-600">
                    Handpicked
                </p>

                <h2 class="mt-2 text-3xl font-black">
                    Featured products
                </h2>
            </div>

            <a
                href="{{ route('store.products.index') }}"
                class="font-semibold text-rose-600 hover:text-rose-700"
            >
                View all →
            </a>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($featuredProducts as $product)
                <x-product-card :product="$product"/>
            @empty
                <div class="col-span-full rounded-2xl bg-white p-10
                            text-center text-gray-500">
                    No featured products available yet.
                </div>
            @endforelse
        </div>
    </section>
@endsection