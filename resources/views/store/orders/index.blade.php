@extends('layouts.store')

@section('title', 'My Orders | StyleHub')

@section('content')
    <section class="bg-gray-950 py-14 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.25em]
                      text-rose-400">
                My account
            </p>

            <h1 class="mt-3 text-4xl font-black">
                My Orders
            </h1>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs
                                       font-semibold uppercase text-gray-500">
                                Order
                            </th>
                            <th class="px-6 py-4 text-left text-xs
                                       font-semibold uppercase text-gray-500">
                                Date
                            </th>
                            <th class="px-6 py-4 text-left text-xs
                                       font-semibold uppercase text-gray-500">
                                Items
                            </th>
                            <th class="px-6 py-4 text-left text-xs
                                       font-semibold uppercase text-gray-500">
                                Total
                            </th>
                            <th class="px-6 py-4 text-left text-xs
                                       font-semibold uppercase text-gray-500">
                                Status
                            </th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-6 py-5 font-semibold">
                                    {{ $order->order_number }}
                                </td>

                                <td class="px-6 py-5 text-gray-600">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>

                                <td class="px-6 py-5 text-gray-600">
                                    {{ $order->items_count }}
                                </td>

                                <td class="px-6 py-5 font-semibold">
                                    {{ number_format(
                                        $order->total_amount,
                                        2
                                    ) }} JOD
                                </td>

                                <td class="px-6 py-5">
                                    <span class="rounded-full px-3 py-1
                                        text-xs font-semibold capitalize
                                        {{ match ($order->status) {
                                            'completed' =>
                                                'bg-green-100 text-green-700',
                                            'cancelled' =>
                                                'bg-red-100 text-red-700',
                                            'processing' =>
                                                'bg-blue-100 text-blue-700',
                                            default =>
                                                'bg-amber-100 text-amber-700',
                                        } }}">
                                        {{ $order->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-right">
                                    <a
                                        href="{{ route('orders.show', $order) }}"
                                        class="font-semibold text-rose-600
                                               hover:text-rose-700"
                                    >
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="px-6 py-16 text-center">
                                    <p class="text-gray-500">
                                        You have not placed any orders yet.
                                    </p>

                                    <a
                                        href="{{ route(
                                            'store.products.index'
                                        ) }}"
                                        class="mt-5 inline-flex rounded-full
                                               bg-gray-950 px-6 py-3
                                               font-semibold text-white"
                                    >
                                        Start Shopping
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    </section>
@endsection