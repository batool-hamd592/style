@extends('layouts.store')

@section('title', 'Shop | StyleHub')

@section('content')
    <section class="bg-gray-950 py-14 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.25em]
                      text-rose-400">
                StyleHub Collection
            </p>

            <h1 class="mt-3 text-4xl font-black">
                Shop all products
            </h1>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <form
            method="GET"
            action="{{ route('store.products.index') }}"
            class="mb-10 grid gap-4 rounded-2xl bg-white p-5
                   shadow-sm md:grid-cols-5"
        >
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search products..."
                class="rounded-lg border-gray-300 md:col-span-2"
            >

            <select name="audience" class="rounded-lg border-gray-300">
                <option value="">Everyone</option>
                <option value="women" @selected(request('audience') === 'women')>
                    Women
                </option>
                <option value="men" @selected(request('audience') === 'men')>
                    Men
                </option>
                <option value="kids" @selected(request('audience') === 'kids')>
                    Kids
                </option>
            </select>

            <select name="category" class="rounded-lg border-gray-300">
                <option value="">All categories</option>

                @foreach ($categories as $category)
                    <option
                        value="{{ $category->slug }}"
                        @selected(request('category') === $category->slug)
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="sort" class="rounded-lg border-gray-300">
                <option value="">Newest</option>
                <option
                    value="price-low"
                    @selected(request('sort') === 'price-low')
                >
                    Price: Low to High
                </option>
                <option
                    value="price-high"
                    @selected(request('sort') === 'price-high')
                >
                    Price: High to Low
                </option>
            </select>

            <div class="flex gap-3 md:col-span-5">
                <button
                    class="rounded-full bg-gray-950 px-6 py-2.5
                           font-semibold text-white"
                >
                    Apply Filters
                </button>

                <a
                    href="{{ route('store.products.index') }}"
                    class="rounded-full border border-gray-300
                           px-6 py-2.5 font-semibold text-gray-700"
                >
                    Reset
                </a>
            </div>
        </form>

        <div class="mb-6 text-sm text-gray-500">
            {{ $products->total() }} products found
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($products as $product)
                <x-product-card :product="$product"/>
            @empty
                <div class="col-span-full rounded-2xl bg-white p-12
                            text-center">
                    <h2 class="text-xl font-bold">
                        No products found
                    </h2>

                    <p class="mt-2 text-gray-500">
                        Try changing your search or filters.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $products->links() }}
        </div>
    </section>
@endsection