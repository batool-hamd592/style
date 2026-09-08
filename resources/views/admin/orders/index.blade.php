<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Orders
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form
                method="GET"
                action="{{ route('admin.orders.index') }}"
                class="mb-6 grid gap-4 rounded-xl bg-white p-5
                       shadow-sm md:grid-cols-4"
            >
                <input
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Order, customer or phone..."
                    class="rounded-lg border-gray-300 md:col-span-2"
                >

                <select name="status" class="rounded-lg border-gray-300">
                    <option value="">All statuses</option>

                    @foreach ([
                        'pending',
                        'processing',
                        'completed',
                        'cancelled'
                    ] as $status)
                        <option
                            value="{{ $status }}"
                            @selected(request('status') === $status)
                        >
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>

                <button
                    class="rounded-lg bg-gray-900 px-5 py-2
                           font-semibold text-white"
                >
                    Filter Orders
                </button>
            </form>

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Order</th>
                                <th class="px-6 py-3 text-left">Customer</th>
                                <th class="px-6 py-3 text-left">Date</th>
                                <th class="px-6 py-3 text-left">Total</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold">
                                            {{ $order->order_number }}
                                        </p>

                                        <p class="text-sm text-gray-500">
                                            {{ $order->items_count }} items
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p>{{ $order->customer_name }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $order->phone }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $order->created_at->format(
                                            'd M Y'
                                        ) }}
                                    </td>

                                    <td class="px-6 py-4 font-semibold">
                                        {{ number_format(
                                            $order->total_amount,
                                            2
                                        ) }} JOD
                                    </td>

                                    <td class="px-6 py-4 capitalize">
                                        {{ $order->status }}
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <a
                                            href="{{ route(
                                                'admin.orders.show',
                                                $order
                                            ) }}"
                                            class="font-semibold text-blue-600"
                                        >
                                            Manage
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="px-6 py-12 text-center
                                               text-gray-500">
                                        No orders found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 p-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>