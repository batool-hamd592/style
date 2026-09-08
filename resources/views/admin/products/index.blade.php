<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Products
            </h2>

            <a
                href="{{ route('admin.products.create') }}"
                class="rounded-lg bg-gray-900 px-4 py-2
                       text-sm font-semibold text-white hover:bg-gray-700"
            >
                Add Product
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-100 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <form
                method="GET"
                action="{{ route('admin.products.index') }}"
                class="mb-6 grid gap-4 rounded-xl bg-white p-5
                       shadow-sm md:grid-cols-4"
            >
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search products..."
                    class="rounded-lg border-gray-300 md:col-span-2"
                >

                <select
                    name="audience"
                    class="rounded-lg border-gray-300"
                >
                    <option value="">All audiences</option>
                    <option value="men" @selected(request('audience') === 'men')>
                        Men
                    </option>
                    <option value="women" @selected(request('audience') === 'women')>
                        Women
                    </option>
                    <option value="kids" @selected(request('audience') === 'kids')>
                        Kids
                    </option>
                </select>

                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="flex-1 rounded-lg bg-gray-900 px-4 py-2
                               font-semibold text-white hover:bg-gray-700"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('admin.products.index') }}"
                        class="rounded-lg border border-gray-300 px-4 py-2
                               text-gray-700 hover:bg-gray-50"
                    >
                        Reset
                    </a>
                </div>
            </form>

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs
                                           font-semibold uppercase text-gray-500">
                                    Product
                                </th>
                                <th class="px-6 py-3 text-left text-xs
                                           font-semibold uppercase text-gray-500">
                                    Category
                                </th>
                                <th class="px-6 py-3 text-left text-xs
                                           font-semibold uppercase text-gray-500">
                                    Price
                                </th>
                                <th class="px-6 py-3 text-left text-xs
                                           font-semibold uppercase text-gray-500">
                                    Stock
                                </th>
                                <th class="px-6 py-3 text-left text-xs
                                           font-semibold uppercase text-gray-500">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs
                                           font-semibold uppercase text-gray-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse ($products as $product)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            @if ($product->image)
                                                <img
                                                    src="{{ Storage::url($product->image) }}"
                                                    alt="{{ $product->name }}"
                                                    class="h-14 w-14 rounded-lg object-cover"
                                                >
                                            @else
                                                <div class="flex h-14 w-14 items-center
                                                            justify-center rounded-lg
                                                            bg-gray-100 text-gray-400">
                                                    No image
                                                </div>
                                            @endif

                                            <div>
                                                <p class="font-semibold text-gray-900">
                                                    {{ $product->name }}
                                                </p>

                                                <p class="text-sm capitalize text-gray-500">
                                                    {{ $product->audience }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $product->category->name }}
                                    </td>

                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ number_format($product->price, 2) }} JOD
                                    </td>

                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $product->variants_sum_stock ?? 0 }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex flex-col items-start gap-1">
                                            @if ($product->is_active)
                                                <span class="rounded-full bg-green-100
                                                             px-3 py-1 text-xs
                                                             font-semibold text-green-700">
                                                    Active
                                                </span>
                                            @else
                                                <span class="rounded-full bg-gray-100
                                                             px-3 py-1 text-xs
                                                             font-semibold text-gray-600">
                                                    Inactive
                                                </span>
                                            @endif

                                            @if ($product->is_featured)
                                                <span class="text-xs font-semibold
                                                             text-amber-600">
                                                    Featured
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-3">
                                            <a
                                                href="{{ route(
                                                    'admin.products.edit',
                                                    $product
                                                ) }}"
                                                class="text-sm font-semibold
                                                       text-blue-600 hover:text-blue-800"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                action="{{ route(
                                                    'admin.products.destroy',
                                                    $product
                                                ) }}"
                                                method="POST"
                                                onsubmit="return confirm(
                                                    'Delete this product?'
                                                )"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="text-sm font-semibold
                                                           text-red-600 hover:text-red-800"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="6"
                                        class="px-6 py-12 text-center text-gray-500"
                                    >
                                        No products found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="border-t border-gray-200 p-4">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>