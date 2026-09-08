<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Edit Product
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-8 shadow-sm">
                <form
                    action="{{ route(
                        'admin.products.update',
                        $product
                    ) }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @method('PUT')

                    @include('admin.products._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>