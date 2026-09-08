@extends('layouts.store')

@section('title', 'Order ' . $order->order_number)

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
        <a
            href="{{ route('orders.index') }}"
            class="font-semibold text-rose-600"
        >
            ← Back to my orders
        </a>

        <div class="mt-6 rounded-2xl bg-white p-7 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row">
                <div>
                    <p class="text-sm text-gray-500">Order number</p>

                    <h1 class="mt-1 text-2xl font-black">
                        {{ $order->order_number }}
                    </h1>

                    <p class="mt-2 text-sm text-gray-500">
                        Placed {{ $order->created_at->format('d M Y, h:i A') }}
                    </p>
                </div>

                <span class="h-fit rounded-full px-4 py-2 text-sm
                    font-semibold capitalize
                    {{ match ($order->status) {
                        'completed' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        'processing' => 'bg-blue-100 text-blue-700',
                        default => 'bg-amber-100 text-amber-700',
                    } }}">
                    {{ $order->status }}
                </span>
            </div>

            <div class="mt-8 divide-y divide-gray-200">
                @foreach ($order->items as $item)
                    <div class="flex justify-between gap-4 py-5">
                        <div>
                            <h2 class="font-bold">
                                {{ $item->product_name }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">
                                Size {{ $item->size }}
                                · {{ $item->color }}
                                · Quantity {{ $item->quantity }}
                            </p>
                        </div>

                        <p class="font-semibold">
                            {{ number_format($item->subtotal, 2) }} JOD
                        </p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 grid gap-6 border-t border-gray-200
                        pt-6 md:grid-cols-2">
                <div>
                    <h2 class="font-bold">Delivery Address</h2>

                    <p class="mt-2 text-gray-600">
                        {{ $order->customer_name }}<br>
                        {{ $order->phone }}<br>
                        {{ $order->city }}<br>
                        {{ $order->address }}
                    </p>
                </div>

                <div class="md:text-right">
                    <p class="text-gray-500">
                        Cash on delivery
                    </p>

                    <p class="mt-2 text-2xl font-black">
                        Total:
                        {{ number_format($order->total_amount, 2) }} JOD
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection