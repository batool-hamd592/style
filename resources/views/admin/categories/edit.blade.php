<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Edit Category
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-8 shadow-sm">
                <form
                    action="{{ route(
                        'admin.categories.update',
                        $category
                    ) }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @method('PUT')

                    @include('admin.categories._form', [
                        'category' => $category
                    ])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>