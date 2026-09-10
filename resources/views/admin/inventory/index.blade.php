<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    Inventory Management
                </h1>

                <p class="mt-2 text-gray-600">
                    Track available, reserved and sold units
                    for every size and color.
                </p>
            </div>

            <div
                class="mb-8 grid gap-5
                       sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6"
            >
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Total Units
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $stats['total_units'] }}
                    </p>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Available
                    </p>

                    <p class="mt-2 text-3xl font-bold text-blue-600">
                        {{ $stats['current_stock'] }}
                    </p>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Reserved
                    </p>

                    <p class="mt-2 text-3xl font-bold text-purple-600">
                        {{ $stats['reserved'] }}
                    </p>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Sold
                    </p>

                    <p class="mt-2 text-3xl font-bold text-green-600">
                        {{ $stats['sold'] }}
                    </p>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Low Stock
                    </p>

                    <p class="mt-2 text-3xl font-bold text-amber-600">
                        {{ $stats['low_stock'] }}
                    </p>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Out of Stock
                    </p>

                    <p class="mt-2 text-3xl font-bold text-red-600">
                        {{ $stats['out_of_stock'] }}
                    </p>
                </div>
            </div>

            <div class="mb-6 rounded-xl bg-white p-5 shadow-sm">
                <form
                    action="{{ route('admin.inventory.index') }}"
                    method="GET"
                    class="grid gap-4
                           md:grid-cols-[1fr_220px_auto]"
                >
                    <div>
                        <label
                            for="search"
                            class="block text-sm font-medium
                                   text-gray-700"
                        >
                            Search
                        </label>

                        <input
                            id="search"
                            name="search"
                            type="search"
                            value="{{ $search }}"
                            placeholder="Product name or SKU"
                            class="mt-2 block w-full rounded-lg
                                   border-gray-300
                                   focus:border-gray-900
                                   focus:ring-gray-900"
                        >
                    </div>

                    <div>
                        <label
                            for="status"
                            class="block text-sm font-medium
                                   text-gray-700"
                        >
                            Stock Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="mt-2 block w-full rounded-lg
                                   border-gray-300
                                   focus:border-gray-900
                                   focus:ring-gray-900"
                        >
                            <option
                                value="all"
                                @selected($status === 'all')
                            >
                                All
                            </option>

                            <option
                                value="in_stock"
                                @selected($status === 'in_stock')
                            >
                                In Stock
                            </option>

                            <option
                                value="low_stock"
                                @selected($status === 'low_stock')
                            >
                                Low Stock
                            </option>

                            <option
                                value="out_of_stock"
                                @selected(
                                    $status === 'out_of_stock'
                                )
                            >
                                Out of Stock
                            </option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="rounded-full bg-gray-950
                                   px-6 py-2.5 font-semibold
                                   text-white transition
                                   hover:bg-rose-600"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route(
                                'admin.inventory.index'
                            ) }}"
                            class="rounded-full border
                                   border-gray-300 px-5 py-2.5
                                   font-semibold text-gray-700
                                   hover:bg-gray-100"
                        >
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div
                class="overflow-hidden rounded-xl
                       bg-white shadow-sm"
            >
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200"
                    >
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Product
                                </th>

                                <th class="px-5 py-4 text-left text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    SKU
                                </th>

                                <th class="px-5 py-4 text-left text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Size
                                </th>

                                <th class="px-5 py-4 text-left text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Color
                                </th>

                                <th class="px-5 py-4 text-center text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Total
                                </th>

                                <th class="px-5 py-4 text-center text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Reserved
                                </th>

                                <th class="px-5 py-4 text-center text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Sold
                                </th>

                                <th class="px-5 py-4 text-center text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Available
                                </th>

                                <th class="px-5 py-4 text-center text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Status
                                </th>

                                <th class="px-5 py-4 text-right text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-gray-100 bg-white"
                        >
                            @forelse ($variants as $variant)
                                @php
                                    $reserved = (int) (
                                        $variant->reserved_quantity ?? 0
                                    );

                                    $sold = (int) (
                                        $variant->sold_quantity ?? 0
                                    );

                                    $total =
                                        (int) $variant->stock
                                        + $reserved
                                        + $sold;
                                @endphp

                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-gray-900">
                                            {{ $variant->product->name }}
                                        </p>

                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $variant->product
                                                ->category->name }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4
                                               text-sm text-gray-600">
                                        {{ $variant->sku }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4
                                               text-sm font-medium">
                                        {{ $variant->size }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4
                                               text-sm text-gray-600">
                                        {{ $variant->color }}
                                    </td>

                                    <td class="px-5 py-4 text-center
                                               font-semibold">
                                        {{ $total }}
                                    </td>

                                    <td class="px-5 py-4 text-center
                                               font-semibold text-purple-600">
                                        {{ $reserved }}
                                    </td>

                                    <td class="px-5 py-4 text-center
                                               font-semibold text-green-600">
                                        {{ $sold }}
                                    </td>

                                    <td class="px-5 py-4 text-center
                                               font-semibold text-blue-600">
                                        {{ $variant->stock }}
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        @if ($variant->stock === 0)
                                            <span
                                                class="inline-flex
                                                       rounded-full
                                                       bg-red-100 px-3 py-1
                                                       text-xs font-semibold
                                                       text-red-700"
                                            >
                                                Out of Stock
                                            </span>
                                        @elseif ($variant->stock <= 3)
                                            <span
                                                class="inline-flex
                                                       rounded-full
                                                       bg-amber-100 px-3 py-1
                                                       text-xs font-semibold
                                                       text-amber-700"
                                            >
                                                Low Stock
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex
                                                       rounded-full
                                                       bg-green-100 px-3 py-1
                                                       text-xs font-semibold
                                                       text-green-700"
                                            >
                                                In Stock
                                            </span>
                                        @endif
                                    </td>

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4 text-right"
                                    >
                                        <a
                                            href="{{ route(
                                                'admin.products.edit',
                                                $variant->product
                                            ) }}"
                                            class="text-sm font-semibold
                                                   text-blue-600
                                                   hover:text-blue-800"
                                        >
                                            Edit Product
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="10"
                                        class="px-6 py-16 text-center
                                               text-gray-500"
                                    >
                                        No inventory items found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($variants->hasPages())
                    <div class="border-t border-gray-200 px-5 py-4">
                        {{ $variants->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>