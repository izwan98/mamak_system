@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Order #{{ $order->id }}</h1>
            <a href="{{ route('checkout.orders') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Back
                to Orders</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-semibold mb-3">Order Information</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p><span class="font-medium">Order ID:</span> #{{ $order->id }}</p>
                    <p><span class="font-medium">Date:</span> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                    <p><span class="font-medium">Subtotal:</span> RM{{ number_format($order->subtotal, 2) }}</p>
                    <p><span class="font-medium">Discount:</span> -RM{{ number_format($order->discount, 2) }}</p>
                    <p class="text-lg font-semibold mt-2"><span>Total:</span> {{ $order->formatted_total }}</p>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold mb-3">Applied Promotions</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    @if ($order->appliedPromotions->isEmpty())
                        <p class="text-gray-500">No promotions applied.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($order->appliedPromotions as $promo)
                                <li class="bg-green-50 p-3 rounded border border-green-100">
                                    <p class="font-medium text-green-700">{{ $promo->promotion_name }}</p>
                                    <p class="text-sm">Discount: <span
                                            class="font-medium">RM{{ number_format($promo->discount_amount, 2) }}</span></p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-3">Order Items</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left">Code</th>
                            <th class="py-3 px-4 text-left">Name</th>
                            <th class="py-3 px-4 text-left">Price</th>
                            <th class="py-3 px-4 text-left">Quantity</th>
                            <th class="py-3 px-4 text-left">Subtotal</th>
                            <th class="py-3 px-4 text-left">Discount</th>
                            <th class="py-3 px-4 text-left">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $item->product_code }}</td>
                                <td class="py-3 px-4">{{ $item->product_name }}</td>
                                <td class="py-3 px-4">RM{{ number_format($item->price, 2) }}</td>
                                <td class="py-3 px-4">{{ $item->quantity }}</td>
                                <td class="py-3 px-4">RM{{ number_format($item->subtotal, 2) }}</td>
                                <td class="py-3 px-4 text-green-600">-RM{{ number_format($item->discount, 2) }}</td>
                                <td class="py-3 px-4 font-semibold">RM{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
