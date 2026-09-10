@extends('layouts.store')

@section('title', $product->name . ' | StyleHub')

@section('content')
    @php
        $variants = $product->variants;

        $colors = $variants
            ->pluck('color')
            ->filter()
            ->unique()
            ->values();

        $hasMultipleColors = $colors->count() > 1;

        $hasAvailableVariants = $variants->contains(
            fn ($variant) => $variant->stock > 0
        );

        $isFavorite = app(
            \App\Services\WishlistService::class
        )->isFavorite($product);
    @endphp

    <section
        class="mx-auto max-w-7xl px-4 py-12
               sm:px-6 lg:px-8"
    >
        <div class="grid gap-12 lg:grid-cols-2">
            <div class="overflow-hidden rounded-3xl bg-gray-100">
                @if ($product->image)
                    <img
                        src="{{ Storage::url($product->image) }}"
                        alt="{{ $product->name }}"
                        class="aspect-[4/5] h-full w-full object-cover"
                    >
                @else
                    <div
                        class="flex aspect-[4/5] items-center
                               justify-center bg-gradient-to-br
                               from-stone-100 to-stone-300
                               text-gray-400"
                    >
                        No image
                    </div>
                @endif
            </div>

            <div class="lg:py-8">
                <p
                    class="text-sm font-semibold uppercase
                           tracking-wider text-rose-600"
                >
                    {{ $product->category->name }}
                    · {{ ucfirst($product->audience) }}
                </p>

                <h1 class="mt-3 text-4xl font-black">
                    {{ $product->name }}
                </h1>

                <p class="mt-5 text-3xl font-bold">
                    {{ number_format($product->price, 2) }} JOD
                </p>

                <p class="mt-7 leading-7 text-gray-600">
                    {{ $product->description }}
                </p>

                <form
                    action="{{ route(
                        'wishlist.toggle',
                        $product
                    ) }}"
                    method="POST"
                    class="mt-5"
                    x-data="{
                        favorite: @js($isFavorite),
                        loading: false,

                        async toggleWishlist(form) {
                            if (this.loading) return;

                            this.loading = true;

                            try {
                                const response = await fetch(
                                    form.action,
                                    {
                                        method: 'POST',
                                        headers: {
                                            'Accept':
                                                'application/json',

                                            'X-CSRF-TOKEN':
                                                form.querySelector(
                                                    '[name=_token]'
                                                ).value
                                        }
                                    }
                                );

                                if (! response.ok) {
                                    throw new Error(
                                        'Unable to update wishlist.'
                                    );
                                }

                                const data =
                                    await response.json();

                                this.favorite = data.added;

                                window.dispatchEvent(
                                    new CustomEvent(
                                        'wishlist-updated',
                                        {
                                            detail: {
                                                count: data.count
                                            }
                                        }
                                    )
                                );
                            } catch (error) {
                                alert(
                                    'Something went wrong. '
                                    + 'Please try again.'
                                );
                            } finally {
                                this.loading = false;
                            }
                        }
                    }"
                    x-on:submit.prevent="
                        toggleWishlist($event.currentTarget)
                    "
                >
                    @csrf

                    <button
                        type="submit"
                        x-bind:disabled="loading"
                        class="inline-flex items-center gap-2
                               rounded-full border border-gray-300
                               px-5 py-2.5 font-semibold transition
                               hover:border-rose-500
                               hover:text-rose-600
                               disabled:cursor-wait
                               disabled:opacity-60"
                    >
                        <span
                            class="text-xl"
                            x-text="favorite ? '❤️' : '♡'"
                        ></span>

                        <span
                            x-text="favorite
                                ? 'Remove from Wishlist'
                                : 'Add to Wishlist'"
                        ></span>
                    </button>
                </form>

                @if (session('success'))
                    <div
                        class="mt-5 rounded-xl bg-green-100
                               p-4 text-green-800"
                    >
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mt-5 rounded-xl bg-red-100
                               p-4 text-red-700"
                    >
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="mt-5 rounded-xl bg-red-100
                               p-4 text-red-700"
                    >
                        {{ $errors->first() }}
                    </div>
                @endif

                <div
                    id="add-to-cart"
                    class="mt-8 scroll-mt-28"
                >
                    @if ($hasAvailableVariants)
                        <form
                            action="{{ route('cart.store') }}"
                            method="POST"
                            class="space-y-5"
                        >
                            @csrf

                            @if (
                                ! $hasMultipleColors
                                && $colors->isNotEmpty()
                            )
                                <div>
                                    <p
                                        class="text-sm font-semibold
                                               text-gray-900"
                                    >
                                        Color
                                    </p>

                                    <span
                                        class="mt-2 inline-flex
                                               rounded-full border
                                               border-gray-300
                                               bg-white px-4 py-2
                                               text-sm font-medium"
                                    >
                                        {{ $colors->first() }}
                                    </span>
                                </div>
                            @endif

                            <div>
                                <label
                                    for="variant_id"
                                    class="block font-semibold
                                           text-gray-900"
                                >
                                    {{ $hasMultipleColors
                                        ? 'Select Size & Color'
                                        : 'Select Size' }}
                                </label>

                                <select
                                    id="variant_id"
                                    name="variant_id"
                                    required
                                    onchange="this.blur()"
                                    onwheel="this.blur()"
                                    class="mt-2 block w-full
                                           rounded-xl border-gray-300
                                           focus:border-gray-900
                                           focus:ring-gray-900"
                                >
                                    <option value="">
                                        {{ $hasMultipleColors
                                            ? 'Choose a size and color'
                                            : 'Choose a size' }}
                                    </option>

                                    @foreach ($variants as $variant)
                                        <option
                                            value="{{ $variant->id }}"
                                            @selected(
                                                old('variant_id')
                                                == $variant->id
                                            )
                                            @disabled(
                                                $variant->stock < 1
                                            )
                                        >
                                            Size {{ $variant->size }}

                                            @if ($hasMultipleColors)
                                                · {{ $variant->color }}
                                            @endif

                                            @if ($variant->stock < 1)
                                                — Out of stock
                                            @elseif ($variant->stock <= 3)
                                                — Only
                                                {{ $variant->stock }}
                                                left
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                @error('variant_id')
                                    <p
                                        class="mt-1 text-sm
                                               text-red-600"
                                    >
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="quantity"
                                    class="block font-semibold
                                           text-gray-900"
                                >
                                    Quantity
                                </label>

                                <input
                                    id="quantity"
                                    name="quantity"
                                    type="number"
                                    min="1"
                                    max="10"
                                    value="{{ old('quantity', 1) }}"
                                    onwheel="this.blur()"
                                    class="mt-2 w-28 rounded-xl
                                           border-gray-300
                                           focus:border-gray-900
                                           focus:ring-gray-900"
                                >

                                @error('quantity')
                                    <p
                                        class="mt-1 text-sm
                                               text-red-600"
                                    >
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="w-full rounded-full
                                       bg-gray-950 px-7 py-4
                                       text-lg font-semibold
                                       text-white transition
                                       hover:bg-rose-600"
                            >
                                Add to Cart
                            </button>
                        </form>
                    @else
                        <div
                            class="rounded-xl bg-red-50
                                   p-4 text-red-700"
                        >
                            This product is currently out of stock.
                        </div>
                    @endif
                </div>

                <div
                    class="mt-8 rounded-xl bg-amber-50
                           p-4 text-sm text-amber-800"
                >
                    Cash on delivery is available across Jordan.
                </div>
            </div>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section
            class="mx-auto max-w-7xl px-4 py-12
                   sm:px-6 lg:px-8"
        >
            <h2 class="mb-8 text-3xl font-black">
                You may also like
            </h2>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($relatedProducts as $relatedProduct)
                    <x-product-card
                        :product="$relatedProduct"
                    />
                @endforeach
            </div>
        </section>
    @endif
@endsection