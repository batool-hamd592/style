@extends('layouts.store')

@section('title', 'Order Confirmed | StyleHub')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-20 sm:px-6">
        <div class="rounded-3xl bg-white p-8 text-center shadow-sm sm:p-12">
            <div class="mx-auto flex h-20 w-20 items-center
                        justify-center rounded-full bg-green-100
                        text-4xl text-green-700">
                ✓
            </div>

            <p class="mt-7 text-sm font-semibold uppercase
                      tracking-[0.25em] text-green-600">
                Order confirmed
            </p>

            <h1 class="mt-3 text-4xl font-black">
                Thank you for your order!
            </h1>

            <p class="mt-4 text-gray-600">
                Your order has been received and will be prepared soon.
            </p>

            <div class="mt-8 rounded-2xl bg-gray-50 p-6 text-left">
                <div class="flex justify-between border-b
                            border-gray-200 pb-4">
                    <span class="text-gray-500">Order number</span>

                    <span class="font-bold">
                        {{ $order->order_number }}
                    </span>
                </div>

                <div class="flex justify-between border-b
                            border-gray-200 py-4">
                    <span class="text-gray-500">Status</span>

                    <span class="font-semibold capitalize text-amber-600">
                        {{ $order->status }}
                    </span>
                </div>

                <div class="flex justify-between border-b
                            border-gray-200 py-4">
                    <span class="text-gray-500">Payment</span>

                    <span class="font-semibold">
                        Cash on delivery
                    </span>
                </div>

                <div class="flex justify-between pt-4">
                    <span class="text-gray-500">Total</span>

                    <span class="text-xl font-bold">
                        {{ number_format(
                            $order->total_amount,
                            2
                        ) }} JOD
                    </span>
                </div>
            </div>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a
                    href="{{ route('store.products.index') }}"
                    class="rounded-full bg-gray-950 px-7 py-3
                           font-semibold text-white hover:bg-rose-600"
                >
                    Continue Shopping
                </a>
            </div>
        </div>
    </section>
@endsection
