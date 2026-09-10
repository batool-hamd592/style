<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'audience' => [
                'nullable',
                'in:men,women,kids',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'sort' => [
                'nullable',
                'in:newest,price-low,price-high',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:24',
            ],
        ]);

        $products = Product::query()
            ->with([
                'category',
                'variants' => fn ($query) =>
                    $query->where('stock', '>', 0),
            ])
            ->withSum('variants', 'stock')
            ->where('is_active', true)
            ->whereHas(
                'category',
                fn (Builder $query) =>
                    $query->where('is_active', true)
            )
            ->when(
                $validated['search'] ?? null,
                function (Builder $query, string $search): void {
                    $query->where(
                        function (Builder $query) use ($search): void {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere(
                                    'description',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $validated['audience'] ?? null,
                fn (Builder $query, string $audience) =>
                    $query->where('audience', $audience)
            )
            ->when(
                $validated['category'] ?? null,
                fn (Builder $query, string $category) =>
                    $query->whereHas(
                        'category',
                        fn (Builder $categoryQuery) =>
                            $categoryQuery->where('slug', $category)
                    )
            )
            ->when(
                ($validated['sort'] ?? 'newest') === 'price-low',
                fn (Builder $query) => $query->orderBy('price')
            )
            ->when(
                ($validated['sort'] ?? 'newest') === 'price-high',
                fn (Builder $query) => $query->orderByDesc('price')
            )
            ->when(
                ($validated['sort'] ?? 'newest') === 'newest',
                fn (Builder $query) => $query->latest()
            )
            ->paginate($validated['per_page'] ?? 12)
            ->withQueryString();

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        abort_unless(
            $product->is_active && $product->category->is_active,
            404
        );

        $product->load([
            'category',
            'variants' => fn ($query) =>
                $query->where('stock', '>', 0)->orderBy('size'),
        ]);

        $product->loadSum('variants', 'stock');

        return new ProductResource($product);
    }
}