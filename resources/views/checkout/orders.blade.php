@extends('layouts.app')

@section('title', 'Orders')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Orders</h1>
            <a href="{{ route('checkout') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Back to
                Checkout</a>
        </div>

        @if ($orders->isEmpty())
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p>No orders found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left">Order ID</th>
                            <th class="py-3 px-4 text-left">Date</th>
                            <th class="py-3 px-4 text-left">Items</th>
                            <th class="py-3 px-4 text-left">Subtotal</th>
                            <th class="py-3 px-4 text-left">Discount</th>
                            <th class="py-3 px-4 text-left">Total</th>
                            <th class="py-3 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr class="border-t">
                                <td class="py-3 px-4">#{{ $order->id }}</td>
                                <td class="py-3 px-4">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3 px-4">{{ $order->items->count() }}</td>
                                <td class="py-3 px-4">RM{{ number_format($order->subtotal, 2) }}</td>
                                <td class="py-3 px-4 text-green-600">-RM{{ number_format($order->discount, 2) }}</td>
                                <td class="py-3 px-4 font-semibold">{{ $order->formatted_total }}</td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('checkout.order-details', $order) }}"
                                        class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
