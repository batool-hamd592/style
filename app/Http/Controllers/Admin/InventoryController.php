<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim(
            (string) $request->query('search', '')
        );

        $status = (string) $request->query(
            'status',
            'all'
        );

        $variants = ProductVariant::query()
            ->with([
                'product.category',
            ])
            ->withSum(
                [
                    'orderItems as reserved_quantity' =>
                        function (Builder $query): void {
                            $query->whereHas(
                                'order',
                                fn (Builder $orderQuery) =>
                                    $orderQuery->whereIn(
                                        'status',
                                        [
                                            'pending',
                                            'processing',
                                        ]
                                    )
                            );
                        },

                    'orderItems as sold_quantity' =>
                        function (Builder $query): void {
                            $query->whereHas(
                                'order',
                                fn (Builder $orderQuery) =>
                                    $orderQuery->where(
                                        'status',
                                        'completed'
                                    )
                            );
                        },
                ],
                'quantity'
            )
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(
                        function (Builder $query) use ($search): void {
                            $query
                                ->where(
                                    'sku',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'product',
                                    fn (Builder $productQuery) =>
                                        $productQuery->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        )
                                );
                        }
                    );
                }
            )
            ->when(
                $status === 'in_stock',
                fn (Builder $query) =>
                    $query->where('stock', '>', 3)
            )
            ->when(
                $status === 'low_stock',
                fn (Builder $query) =>
                    $query->whereBetween('stock', [1, 3])
            )
            ->when(
                $status === 'out_of_stock',
                fn (Builder $query) =>
                    $query->where('stock', 0)
            )
            ->orderBy('stock')
            ->orderBy('product_id')
            ->paginate(15)
            ->withQueryString();

        $currentStock = (int) ProductVariant::query()
            ->sum('stock');

        $reservedQuantity = (int) OrderItem::query()
            ->whereNotNull('product_variant_id')
            ->whereHas(
                'order',
                fn (Builder $query) =>
                    $query->whereIn(
                        'status',
                        [
                            'pending',
                            'processing',
                        ]
                    )
            )
            ->sum('quantity');

        $soldQuantity = (int) OrderItem::query()
            ->whereNotNull('product_variant_id')
            ->whereHas(
                'order',
                fn (Builder $query) =>
                    $query->where(
                        'status',
                        'completed'
                    )
            )
            ->sum('quantity');

        $stats = [
            'current_stock' => $currentStock,

            'reserved' => $reservedQuantity,

            'sold' => $soldQuantity,

            'total_units' =>
                $currentStock
                + $reservedQuantity
                + $soldQuantity,

            'low_stock' => ProductVariant::query()
                ->whereBetween('stock', [1, 3])
                ->count(),

            'out_of_stock' => ProductVariant::query()
                ->where('stock', 0)
                ->count(),
        ];

        return view(
            'admin.inventory.index',
            compact(
                'variants',
                'stats',
                'search',
                'status'
            )
        );
    }
}