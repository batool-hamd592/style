@props(['product'])

<article class="group overflow-hidden rounded-2xl bg-white shadow-sm
                transition hover:-translate-y-1 hover:shadow-lg">
    <a href="{{ route('store.products.show', $product) }}">
        <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
            @if ($product->image)
                <img
                    src="{{ Storage::url($product->image) }}"
                    alt="{{ $product->name }}"
                    class="h-full w-full object-cover transition
                           duration-500 group-hover:scale-105"
                >
            @else
                <div class="flex h-full items-center justify-center
                            bg-gradient-to-br from-stone-100 to-stone-200
                            text-gray-400">
                    No image
                </div>
            @endif

            @if ($product->is_featured)
                <span class="absolute left-3 top-3 rounded-full
                             bg-rose-600 px-3 py-1 text-xs
                             font-semibold text-white">
                    Featured
                </span>
            @endif

            @if (($product->variants_sum_stock ?? 0) === 0)
                <span class="absolute inset-x-3 bottom-3 rounded-lg
                             bg-gray-950/85 px-3 py-2 text-center
                             text-sm font-semibold text-white">
                    Out of stock
                </span>
            @endif
        </div>

        <div class="p-5">
            <p class="text-xs font-semibold uppercase tracking-wider
                      text-rose-600">
                {{ $product->category->name }}
            </p>

            <h3 class="mt-2 font-semibold text-gray-900">
                {{ $product->name }}
            </h3>

            <div class="mt-3 flex items-center justify-between">
                <p class="text-lg font-bold">
                    {{ number_format($product->price, 2) }} JOD
                </p>

                <span class="text-sm capitalize text-gray-500">
                    {{ $product->audience }}
                </span>
            </div>
        </div>
    </a>
</article>