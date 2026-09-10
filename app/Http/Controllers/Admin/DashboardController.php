<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $orderStats = Order::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                "SUM(CASE WHEN status = 'pending'
                THEN 1 ELSE 0 END) as pending"
            )
            ->selectRaw(
                "SUM(CASE WHEN status = 'processing'
                THEN 1 ELSE 0 END) as processing"
            )
            ->selectRaw(
                "SUM(CASE WHEN status = 'completed'
                THEN 1 ELSE 0 END) as completed"
            )
            ->selectRaw(
                "SUM(CASE WHEN status = 'cancelled'
                THEN 1 ELSE 0 END) as cancelled"
            )
            ->first();

        $stats = [
            'categories' => Category::query()->count(),

            'products' => Product::query()->count(),

            'orders' => (int) ($orderStats->total ?? 0),

            'customers' => User::query()
                ->where('role', 'customer')
                ->count(),

            'pending_orders' =>
                (int) ($orderStats->pending ?? 0),

            'processing_orders' =>
                (int) ($orderStats->processing ?? 0),

            'completed_orders' =>
                (int) ($orderStats->completed ?? 0),

            'cancelled_orders' =>
                (int) ($orderStats->cancelled ?? 0),
        ];

        return view(
            'admin.dashboard',
            compact('stats')
        );
    }
}