@props(['product'])

@php
    $isFavorite = app(
        \App\Services\WishlistService::class
    )->isFavorite($product);

    $availableStock = (int) (
        $product->variants_sum_stock ?? 0
    );
@endphp

<article
    class="group flex h-full flex-col overflow-hidden
           rounded-2xl bg-white shadow-sm transition
           hover:-translate-y-1 hover:shadow-lg"
>
    <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
        <a href="{{ route('store.products.show', $product) }}">
            @if ($product->image)
                <img
                    src="{{ Storage::url($product->image) }}"
                    alt="{{ $product->name }}"
                    class="h-full w-full object-cover transition
                           duration-500 group-hover:scale-105"
                >
            @else
                <div
                    class="flex h-full items-center justify-center
                           bg-gradient-to-br from-stone-100
                           to-stone-200 text-gray-400"
                >
                    No image
                </div>
            @endif
        </a>

        <form
            action="{{ route('wishlist.toggle', $product) }}"
            method="POST"
            class="absolute right-3 top-3"
            x-data="{
                favorite: @js($isFavorite),
                loading: false,

                async toggleWishlist(form) {
                    if (this.loading) return;

                    this.loading = true;

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN':
                                    form.querySelector(
                                        '[name=_token]'
                                    ).value
                            }
                        });

                        if (! response.ok) {
                            throw new Error(
                                'Unable to update wishlist.'
                            );
                        }

                        const data = await response.json();

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
                            'Something went wrong. Please try again.'
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
                x-bind:aria-label="favorite
                    ? 'Remove from wishlist'
                    : 'Add to wishlist'"
                class="flex h-10 w-10 items-center justify-center
                       rounded-full bg-white text-xl shadow-md
                       transition hover:scale-110
                       disabled:cursor-wait disabled:opacity-60"
            >
                <span
                    x-text="favorite ? '❤️' : '♡'"
                ></span>
            </button>
        </form>

        @if ($product->is_featured)
            <span
                class="absolute left-3 top-3 rounded-full
                       bg-rose-600 px-3 py-1 text-xs
                       font-semibold text-white"
            >
                Featured
            </span>
        @endif

        @if ($availableStock === 0)
            <span
                class="absolute inset-x-3 bottom-3 rounded-lg
                       bg-gray-950/85 px-3 py-2 text-center
                       text-sm font-semibold text-white"
            >
                Out of stock
            </span>
        @elseif ($availableStock <= 3)
            <span
                class="absolute bottom-3 left-3 rounded-full
                       bg-amber-500 px-3 py-1.5 text-xs
                       font-bold text-white shadow"
            >
                Only {{ $availableStock }} left
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center justify-between gap-3">
            <p
                class="text-xs font-semibold uppercase
                       tracking-wider text-rose-600"
            >
                {{ $product->category->name }}
            </p>

            <span class="text-xs capitalize text-gray-500">
                {{ $product->audience }}
            </span>
        </div>

        <a href="{{ route('store.products.show', $product) }}">
            <h3
                class="mt-2 font-semibold text-gray-900
                       transition hover:text-rose-600"
            >
                {{ $product->name }}
            </h3>
        </a>

        <div class="mt-auto flex items-center justify-between pt-4">
    <p class="text-lg font-bold">
        {{ number_format($product->price, 2) }} JOD
    </p>

    @if ($availableStock > 0)
        <a
            href="{{ route(
                'store.products.show',
                $product
            ) }}#add-to-cart"
            aria-label="Add to cart"
            title="Add to Cart"
            class="text-2xl transition hover:scale-110"
        >
            🛒
        </a>
    @endif
</div>
    </div>
</article>