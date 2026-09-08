<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Order {{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto grid max-w-7xl gap-7 px-4
                    sm:px-6 lg:grid-cols-3 lg:px-8">
            <div class="rounded-xl bg-white p-7 shadow-sm lg:col-span-2">
                <h2 class="text-xl font-bold">Order Items</h2>

                <div class="mt-5 divide-y divide-gray-200">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between gap-4 py-5">
                            <div>
                                <p class="font-semibold">
                                    {{ $item->product_name }}
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    Size {{ $item->size }}
                                    · {{ $item->color }}
                                    · Qty {{ $item->quantity }}
                                </p>
                            </div>

                            <p class="font-semibold">
                                {{ number_format(
                                    $item->subtotal,
                                    2
                                ) }} JOD
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <h2 class="font-bold">Update Status</h2>

                    @if (session('success'))
                        <div class="mt-4 rounded-lg bg-green-100
                                    p-3 text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mt-4 rounded-lg bg-red-100
                                    p-3 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form
                        action="{{ route(
                            'admin.orders.status',
                            $order
                        ) }}"
                        method="POST"
                        class="mt-5 space-y-4"
                    >
                        @csrf
                        @method('PATCH')

                        <select
                            name="status"
                            class="block w-full rounded-lg border-gray-300"
                            @disabled(in_array(
                                $order->status,
                                ['completed', 'cancelled']
                            ))
                        >
                            @foreach ([
                                'pending',
                                'processing',
                                'completed',
                                'cancelled'
                            ] as $status)
                                <option
                                    value="{{ $status }}"
                                    @selected($order->status === $status)
                                >
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>

                        @unless (in_array(
                            $order->status,
                            ['completed', 'cancelled']
                        ))
                            <button
                                class="w-full rounded-lg bg-gray-900
                                       px-5 py-3 font-semibold text-white"
                            >
                                Update Status
                            </button>
                        @endunless
                    </form>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <h2 class="font-bold">Customer & Delivery</h2>

                    <div class="mt-4 space-y-2 text-sm text-gray-600">
                        <p>{{ $order->customer_name }}</p>
                        <p>{{ $order->user->email }}</p>
                        <p>{{ $order->phone }}</p>
                        <p>{{ $order->city }}</p>
                        <p>{{ $order->address }}</p>
                    </div>

                    <div class="mt-5 border-t border-gray-200 pt-5">
                        <p class="text-sm text-gray-500">
                            Cash on delivery
                        </p>

                        <p class="mt-1 text-2xl font-black">
                            {{ number_format(
                                $order->total_amount,
                                2
                            ) }} JOD
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
