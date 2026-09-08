<div class="space-y-6">
    <div>
        <label for="name"
               class="block text-sm font-medium text-gray-700">
            Category Name
        </label>

        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $category?->name) }}"
            class="mt-1 block w-full rounded-lg border-gray-300
                   shadow-sm focus:border-gray-900 focus:ring-gray-900"
        >

        @error('name')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label for="description"
               class="block text-sm font-medium text-gray-700">
            Description
        </label>

        <textarea
            id="description"
            name="description"
            rows="5"
            class="mt-1 block w-full rounded-lg border-gray-300
                   shadow-sm focus:border-gray-900 focus:ring-gray-900"
        >{{ old('description', $category?->description) }}</textarea>

        @error('description')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label for="image"
               class="block text-sm font-medium text-gray-700">
            Category Image
        </label>

        <input
            id="image"
            name="image"
            type="file"
            accept=".jpg,.jpeg,.png,.webp"
            class="mt-1 block w-full text-sm text-gray-600"
        >

        @error('image')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror

        @if ($category?->image)
            <img
                src="{{ Storage::url($category->image) }}"
                alt="{{ $category->name }}"
                class="mt-4 h-24 w-24 rounded-lg object-cover"
            >
        @endif
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">

        <input
            id="is_active"
            name="is_active"
            type="checkbox"
            value="1"
            @checked(old('is_active', $category?->is_active ?? true))
            class="rounded border-gray-300 text-gray-900
                   focus:ring-gray-900"
        >

        <label for="is_active" class="text-sm text-gray-700">
            Active
        </label>
    </div>

    <div class="flex gap-3">
        <button
            type="submit"
            class="rounded-lg bg-gray-900 px-5 py-2.5
                   text-sm font-semibold text-white hover:bg-gray-700"
        >
            Save Category
        </button>

        <a
            href="{{ route('admin.categories.index') }}"
            class="rounded-lg border border-gray-300 px-5 py-2.5
                   text-sm font-semibold text-gray-700 hover:bg-gray-50"
        >
            Cancel
        </a>
    </div>
</div>