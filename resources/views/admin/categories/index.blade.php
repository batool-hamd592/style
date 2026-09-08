<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Categories
            </h2>

            <a
                href="{{ route('admin.categories.create') }}"
                class="rounded-lg bg-gray-900 px-4 py-2
                       text-sm font-semibold text-white hover:bg-gray-700"
            >
                Add Category
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

            @if (session('error'))
                <div class="mb-6 rounded-lg bg-red-100 p-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs
                                           font-semibold uppercase text-gray-500">
                                    Category
                                </th>

                                <th class="px-6 py-3 text-left text-xs
                                           font-semibold uppercase text-gray-500">
                                    Products
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

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($categories as $category)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            @if ($category->image)
                                                <img
                                                    src="{{ Storage::url(
                                                        $category->image
                                                    ) }}"
                                                    alt="{{ $category->name }}"
                                                    class="h-12 w-12 rounded-lg object-cover"
                                                >
                                            @else
                                                <div class="flex h-12 w-12
                                                            items-center justify-center
                                                            rounded-lg bg-gray-100
                                                            text-lg font-bold text-gray-500">
                                                    {{ substr($category->name, 0, 1) }}
                                                </div>
                                            @endif

                                            <div>
                                                <p class="font-semibold text-gray-900">
                                                    {{ $category->name }}
                                                </p>

                                                <p class="text-sm text-gray-500">
                                                    {{ $category->slug }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $category->products_count }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($category->is_active)
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
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-3">
                                            <a
                                                href="{{ route(
                                                    'admin.categories.edit',
                                                    $category
                                                ) }}"
                                                class="text-sm font-semibold
                                                       text-blue-600 hover:text-blue-800"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                action="{{ route(
                                                    'admin.categories.destroy',
                                                    $category
                                                ) }}"
                                                method="POST"
                                                onsubmit="return confirm(
                                                    'Delete this category?'
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
                                        colspan="4"
                                        class="px-6 py-12 text-center text-gray-500"
                                    >
                                        No categories found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($categories->hasPages())
                    <div class="border-t border-gray-200 p-4">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>