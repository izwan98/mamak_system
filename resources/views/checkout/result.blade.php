@extends('layouts.app')

@section('title', 'Checkout Result')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Checkout Completed</h1>
            <a href="{{ route('checkout') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Back to
                Checkout</a>
        </div>

        <div class="bg-green-50 p-4 rounded-lg mb-6">
            <h2 class="text-lg font-semibold text-green-700 mb-4">Order Summary</h2>

            <div class="mb-4">
                <h3 class="font-medium mb-2">Items</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach ($items as $item)
                        <div class="bg-white p-3 rounded-lg border">
                            <span class="text-gray-600 text-sm">{{ $item->code }}</span>
                            <p class="font-medium">{{ $item->name }}</p>
                            <p class="text-blue-600">{{ $item->formatted_price }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-4 text-right">
                <p class="text-xl font-bold">Total: RM{{ number_format($total, 2) }}</p>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('checkout') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">New
                Checkout</a>
            <a href="{{ route('checkout.orders') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">View
                All Orders</a>
        </div>
    </div>
@endsection
