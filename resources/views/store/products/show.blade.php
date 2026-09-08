@extends('layouts.store')

@section('title', $product->name . ' | StyleHub')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-2">
            <div class="overflow-hidden rounded-3xl bg-gray-100">
                @if ($product->image)
                    <img
                        src="{{ Storage::url($product->image) }}"
                        alt="{{ $product->name }}"
                        class="aspect-[4/5] h-full w-full object-cover"
                    >
                @else
                    <div class="flex aspect-[4/5] items-center
                                justify-center bg-gradient-to-br
                                from-stone-100 to-stone-300 text-gray-400">
                        No image
                    </div>
                @endif
            </div>

            <div class="lg:py-8">
                <p class="text-sm font-semibold uppercase tracking-wider
                          text-rose-600">
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

                <div class="mt-8">
                    <h2 class="font-semibold">Available options</h2>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @forelse ($product->variants as $variant)
                            <div class="rounded-xl border border-gray-200
                                        bg-white p-4">
                                <div class="flex justify-between">
                                    <span class="font-semibold">
                                        Size {{ $variant->size }}
                                    </span>

                                    <span class="text-sm text-gray-500">
                                        {{ $variant->stock }} available
                                    </span>
                                </div>

                                <p class="mt-1 text-sm text-gray-600">
                                    Color: {{ $variant->color }}
                                </p>
                            </div>
                        @empty
                            <div class="rounded-xl bg-red-50 p-4
                                        text-red-700 sm:col-span-2">
                                This product is currently out of stock.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-8 rounded-xl bg-amber-50 p-4
                            text-sm text-amber-800">
                    Cash on delivery is available across Jordan.
                </div>
            </div>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <h2 class="mb-8 text-3xl font-black">
                You may also like
            </h2>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($relatedProducts as $relatedProduct)
                    <x-product-card :product="$relatedProduct"/>
                @endforeach
            </div>
        </section>
    @endif
@endsection