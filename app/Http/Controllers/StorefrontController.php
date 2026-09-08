<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function home(): View
    {
        $featuredProducts = Product::query()
            ->with('category')
            ->withSum('variants', 'stock')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount([
                'products' => fn (Builder $query) =>
                    $query->where('is_active', true),
            ])
            ->orderBy('name')
            ->get();

        return view('store.home', compact(
            'featuredProducts',
            'categories'
        ));
    }

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->withSum('variants', 'stock')
            ->where('is_active', true)
            ->whereHas(
                'category',
                fn (Builder $query) =>
                    $query->where('is_active', true)
            )
            ->when(
                $request->filled('search'),
                fn (Builder $query) => $query->where(
                    function (Builder $query) use ($request): void {
                        $query
                            ->where(
                                'name',
                                'like',
                                '%' . $request->string('search') . '%'
                            )
                            ->orWhere(
                                'description',
                                'like',
                                '%' . $request->string('search') . '%'
                            );
                    }
                )
            )
            ->when(
                $request->filled('audience'),
                fn (Builder $query) => $query->where(
                    'audience',
                    $request->string('audience')
                )
            )
            ->when(
                $request->filled('category'),
                fn (Builder $query) => $query->whereHas(
                    'category',
                    fn (Builder $categoryQuery) =>
                        $categoryQuery->where(
                            'slug',
                            $request->string('category')
                        )
                )
            )
            ->when(
                $request->input('sort') === 'price-low',
                fn (Builder $query) => $query->orderBy('price')
            )
            ->when(
                $request->input('sort') === 'price-high',
                fn (Builder $query) => $query->orderByDesc('price')
            )
            ->when(
                ! in_array(
                    $request->input('sort'),
                    ['price-low', 'price-high'],
                    true
                ),
                fn (Builder $query) => $query->latest()
            )
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('store.products.index', compact(
            'products',
            'categories'
        ));
    }

    public function show(Product $product): View
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

        $relatedProducts = Product::query()
            ->withSum('variants', 'stock')
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->take(4)
            ->get();

        return view('store.products.show', compact(
            'product',
            'relatedProducts'
        ));
    }
}