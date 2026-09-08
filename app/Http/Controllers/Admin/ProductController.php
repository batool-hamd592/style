<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category')
            ->withSum('variants', 'stock')
            ->when(
                request('search'),
                fn ($query, $search) => $query->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
            )
            ->when(
                request('audience'),
                fn ($query, $audience) => $query->where(
                    'audience',
                    $audience
                )
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $variantRows = old('variants', [
            [
                'size' => '',
                'color' => '',
                'stock' => 0,
            ],
        ]);

        return view('admin.products.create', compact(
            'categories',
            'variantRows'
        ));
    }

    public function store(
        StoreProductRequest $request
    ): RedirectResponse {
        $imagePath = $request->file('image')
            ->store('products', 'public');

        try {
            DB::transaction(function () use ($request, $imagePath): void {
                $product = Product::create([
                    'category_id' => $request->validated('category_id'),
                    'name' => $request->validated('name'),
                    'slug' => Str::slug($request->validated('name')),
                    'description' => $request->validated('description'),
                    'price' => $request->validated('price'),
                    'audience' => $request->validated('audience'),
                    'image' => $imagePath,
                    'is_active' => $request->boolean('is_active'),
                    'is_featured' => $request->boolean('is_featured'),
                ]);

                foreach ($request->validated('variants') as $variant) {
                    $product->variants()->create([
                        'sku' => $this->generateSku(
                            $product,
                            $variant['size'],
                            $variant['color']
                        ),
                        'size' => trim($variant['size']),
                        'color' => trim($variant['color']),
                        'stock' => $variant['stock'],
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($imagePath);

            throw $exception;
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $product->load('variants');

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $variantRows = old(
            'variants',
            $product->variants
                ->map(fn ($variant) => [
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'stock' => $variant->stock,
                ])
                ->values()
                ->all()
        );

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'variantRows'
        ));
    }

    public function update(
        UpdateProductRequest $request,
        Product $product
    ): RedirectResponse {
        $oldImage = $product->image;
        $newImage = null;

        if ($request->hasFile('image')) {
            $newImage = $request->file('image')
                ->store('products', 'public');
        }

        try {
            DB::transaction(function () use (
                $request,
                $product,
                $newImage
            ): void {
                $product->update([
                    'category_id' => $request->validated('category_id'),
                    'name' => $request->validated('name'),
                    'slug' => Str::slug($request->validated('name')),
                    'description' => $request->validated('description'),
                    'price' => $request->validated('price'),
                    'audience' => $request->validated('audience'),
                    'image' => $newImage ?? $product->image,
                    'is_active' => $request->boolean('is_active'),
                    'is_featured' => $request->boolean('is_featured'),
                ]);

                $product->variants()->delete();

                foreach ($request->validated('variants') as $variant) {
                    $product->variants()->create([
                        'sku' => $this->generateSku(
                            $product,
                            $variant['size'],
                            $variant['color']
                        ),
                        'size' => trim($variant['size']),
                        'color' => trim($variant['color']),
                        'stock' => $variant['stock'],
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        if ($newImage && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    private function generateSku(
        Product $product,
        string $size,
        string $color
    ): string {
        $productCode = Str::upper(
            Str::substr(Str::slug($product->name, ''), 0, 5)
        );

        $sizeCode = Str::upper(
            Str::substr(Str::slug($size, ''), 0, 4)
        );

        $colorCode = Str::upper(
            Str::substr(Str::slug($color, ''), 0, 4)
        );

        return implode('-', [
            'SH',
            $productCode,
            $product->id,
            $sizeCode,
            $colorCode,
            Str::upper(Str::random(4)),
        ]);
    }
}