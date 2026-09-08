<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when(
                $request->filled('search'),
                function (Builder $query) use ($request): void {
                    $search = $request->string('search');

                    $query->where(function (Builder $query) use ($search): void {
                        $query
                            ->where('order_number', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
            )
            ->when(
                $request->filled('status'),
                fn (Builder $query) => $query->where(
                    'status',
                    $request->string('status')
                )
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load([
            'user',
            'items.variant',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order
    ): RedirectResponse {
        $newStatus = $request->validated('status');

        $allowedTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        if ($newStatus === $order->status) {
            return back()->with(
                'success',
                'The order status has not changed.'
            );
        }

        if (! in_array(
            $newStatus,
            $allowedTransitions[$order->status],
            true
        )) {
            return back()->with(
                'error',
                "Order status cannot change from " .
                "{$order->status} to {$newStatus}."
            );
        }

        DB::transaction(function () use ($order, $newStatus): void {
            $order->load('items.variant');

            if ($newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->variant) {
                        $item->variant->increment(
                            'stock',
                            $item->quantity
                        );
                    }
                }
            }

            $order->update([
                'status' => $newStatus,
            ]);
        });

        return back()->with(
            'success',
            'Order status updated successfully.'
        );
    }
}