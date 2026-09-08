<div
    class="space-y-8"
    x-data="{ variants: @js($variantRows) }"
>
    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="name"
                   class="block text-sm font-medium text-gray-700">
                Product Name
            </label>

            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $product?->name) }}"
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
            <label for="category_id"
                   class="block text-sm font-medium text-gray-700">
                Category
            </label>

            <select
                id="category_id"
                name="category_id"
                class="mt-1 block w-full rounded-lg border-gray-300
                       shadow-sm focus:border-gray-900 focus:ring-gray-900"
            >
                <option value="">Choose a category</option>

                @foreach ($categories as $category)
                    <option
                        value="{{ $category->id }}"
                        @selected(
                            old(
                                'category_id',
                                $product?->category_id
                            ) == $category->id
                        )
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            @error('category_id')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="price"
                   class="block text-sm font-medium text-gray-700">
                Price (JOD)
            </label>

            <input
                id="price"
                name="price"
                type="number"
                min="0.01"
                step="0.01"
                value="{{ old('price', $product?->price) }}"
                class="mt-1 block w-full rounded-lg border-gray-300
                       shadow-sm focus:border-gray-900 focus:ring-gray-900"
            >

            @error('price')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="audience"
                   class="block text-sm font-medium text-gray-700">
                Audience
            </label>

            <select
                id="audience"
                name="audience"
                class="mt-1 block w-full rounded-lg border-gray-300
                       shadow-sm focus:border-gray-900 focus:ring-gray-900"
            >
                <option value="">Choose audience</option>

                <option
                    value="men"
                    @selected(old('audience', $product?->audience) === 'men')
                >
                    Men
                </option>

                <option
                    value="women"
                    @selected(old('audience', $product?->audience) === 'women')
                >
                    Women
                </option>

                <option
                    value="kids"
                    @selected(old('audience', $product?->audience) === 'kids')
                >
                    Kids
                </option>
            </select>

            @error('audience')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
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
        >{{ old('description', $product?->description) }}</textarea>

        @error('description')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label for="image"
               class="block text-sm font-medium text-gray-700">
            Product Image
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

        @if ($product?->image)
            <img
                src="{{ Storage::url($product->image) }}"
                alt="{{ $product->name }}"
                class="mt-4 h-32 w-32 rounded-xl object-cover"
            >
        @endif
    </div>

    <div class="flex flex-wrap gap-6">
        <label class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">

            <input
                name="is_active"
                type="checkbox"
                value="1"
                @checked(old('is_active', $product?->is_active ?? true))
                class="rounded border-gray-300 text-gray-900
                       focus:ring-gray-900"
            >

            <span class="text-sm text-gray-700">Active</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="hidden" name="is_featured" value="0">

            <input
                name="is_featured"
                type="checkbox"
                value="1"
                @checked(old('is_featured', $product?->is_featured ?? false))
                class="rounded border-gray-300 text-gray-900
                       focus:ring-gray-900"
            >

            <span class="text-sm text-gray-700">Featured Product</span>
        </label>
    </div>

    <div class="border-t border-gray-200 pt-8">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    Sizes, Colors and Stock
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Add at least one available product option.
                </p>
            </div>

            <button
                type="button"
                x-on:click="variants.push({
                    size: '',
                    color: '',
                    stock: 0
                })"
                class="rounded-lg bg-gray-900 px-4 py-2
                       text-sm font-semibold text-white hover:bg-gray-700"
            >
                Add Option
            </button>
        </div>

        @error('variants')
            <div class="mb-4 rounded-lg bg-red-100 p-3 text-sm text-red-700">
                {{ $message }}
            </div>
        @enderror

        @if ($errors->has('variants.*'))
            <div class="mb-4 rounded-lg bg-red-100 p-3 text-sm text-red-700">
                Please complete every size, color and stock field correctly.
            </div>
        @endif

        <div class="space-y-4">
            <template
                x-for="(variant, index) in variants"
                x-bind:key="index"
            >
                <div class="grid items-end gap-4 rounded-xl
                            border border-gray-200 bg-gray-50 p-4
                            md:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Size
                        </label>

                        <input
                            type="text"
                            x-model="variant.size"
                            x-bind:name="'variants[' + index + '][size]'"
                            placeholder="S, M, L, XL..."
                            class="mt-1 block w-full rounded-lg
                                   border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Color
                        </label>

                        <input
                            type="text"
                            x-model="variant.color"
                            x-bind:name="'variants[' + index + '][color]'"
                            placeholder="Black"
                            class="mt-1 block w-full rounded-lg
                                   border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Stock
                        </label>

                        <input
                            type="number"
                            min="0"
                            x-model="variant.stock"
                            x-bind:name="'variants[' + index + '][stock]'"
                            class="mt-1 block w-full rounded-lg
                                   border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <button
                            type="button"
                            x-on:click="variants.splice(index, 1)"
                            x-bind:disabled="variants.length === 1"
                            class="w-full rounded-lg border border-red-200
                                   px-4 py-2 text-sm font-semibold text-red-600
                                   hover:bg-red-50 disabled:cursor-not-allowed
                                   disabled:opacity-40"
                        >
                            Remove
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="flex gap-3 border-t border-gray-200 pt-6">
        <button
            type="submit"
            class="rounded-lg bg-gray-900 px-6 py-3
                   text-sm font-semibold text-white hover:bg-gray-700"
        >
            Save Product
        </button>

        <a
            href="{{ route('admin.products.index') }}"
            class="rounded-lg border border-gray-300 px-6 py-3
                   text-sm font-semibold text-gray-700 hover:bg-gray-50"
        >
            Cancel
        </a>
    </div>
</div>