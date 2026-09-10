<x-app-layout>
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

            <div class="mb-5 mt-10">
                <h2 class="text-2xl font-bold text-gray-900">
                    Order Status
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Select a status to view its orders.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <a
                    href="{{ route('admin.orders.index', [
                        'status' => 'pending'
                    ]) }}"
                    class="rounded-xl border-l-4 border-amber-500
                           bg-white p-6 shadow-sm transition
                           hover:-translate-y-1 hover:shadow-md"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Pending
                            </p>

                            <p
                                class="mt-2 text-3xl font-bold
                                       text-amber-600"
                            >
                                {{ $stats['pending_orders'] }}
                            </p>
                        </div>

                        <span class="text-3xl">⏳</span>
                    </div>
                </a>

                <a
                    href="{{ route('admin.orders.index', [
                        'status' => 'processing'
                    ]) }}"
                    class="rounded-xl border-l-4 border-blue-500
                           bg-white p-6 shadow-sm transition
                           hover:-translate-y-1 hover:shadow-md"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Processing
                            </p>

                            <p
                                class="mt-2 text-3xl font-bold
                                       text-blue-600"
                            >
                                {{ $stats['processing_orders'] }}
                            </p>
                        </div>

                        <span class="text-3xl">📦</span>
                    </div>
                </a>

                <a
                    href="{{ route('admin.orders.index', [
                        'status' => 'completed'
                    ]) }}"
                    class="rounded-xl border-l-4 border-green-500
                           bg-white p-6 shadow-sm transition
                           hover:-translate-y-1 hover:shadow-md"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Completed
                            </p>

                            <p
                                class="mt-2 text-3xl font-bold
                                       text-green-600"
                            >
                                {{ $stats['completed_orders'] }}
                            </p>
                        </div>

                        <span class="text-3xl">✅</span>
                    </div>
                </a>

                <a
                    href="{{ route('admin.orders.index', [
                        'status' => 'cancelled'
                    ]) }}"
                    class="rounded-xl border-l-4 border-red-500
                           bg-white p-6 shadow-sm transition
                           hover:-translate-y-1 hover:shadow-md"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Cancelled
                            </p>

                            <p
                                class="mt-2 text-3xl font-bold
                                       text-red-600"
                            >
                                {{ $stats['cancelled_orders'] }}
                            </p>
                        </div>

                        <span class="text-3xl">✕</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>