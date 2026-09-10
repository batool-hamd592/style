<x-mail::message>
# Your Order Is Confirmed

Hello {{ $order->customer_name }},

Thank you for shopping with StyleHub. We have received your order and will begin preparing it soon.

**Order Number:** {{ $order->order_number }}

**Order Date:** {{ $order->created_at->format('d M Y, h:i A') }}

**Status:** {{ ucfirst($order->status) }}

## Order Items

@foreach ($order->items as $item)
**{{ $item->product_name }}**

Size: {{ $item->size }}  
Color: {{ $item->color }}  
Quantity: {{ $item->quantity }}  
Subtotal: {{ number_format($item->subtotal, 2) }} JOD

---

@endforeach

**Delivery Address**

{{ $order->city }}  
{{ $order->address }}  
Phone: {{ $order->phone }}

**Payment Method:** Cash on delivery

# Total: {{ number_format($order->total_amount, 2) }} JOD

<x-mail::button :url="route('orders.show', $order)">
View Your Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>