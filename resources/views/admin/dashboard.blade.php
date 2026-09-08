<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Admin Dashboard
            </h2>

            <span class="rounded-full bg-gray-900 px-3 py-1 text-sm text-white">
                {{ auth()->user()->name }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    Welcome back, {{ auth()->user()->name }}
                </h1>

                <p class="mt-2 text-gray-600">
                    Here is an overview of your StyleHub store.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Categories
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $stats['categories'] }}
                    </p>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Products
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $stats['products'] }}
                    </p>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Orders
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $stats['orders'] }}
                    </p>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">
                        Customers
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $stats['customers'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
