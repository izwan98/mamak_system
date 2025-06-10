@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Welcome to CapBay Mamak</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-50 p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-3">Products</h2>
                <p class="mb-4">Manage your products including code, name, and price.</p>
                <a href="{{ route('products.index') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Manage Products</a>
            </div>

            <div class="bg-green-50 p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-3">Promotions</h2>
                <p class="mb-4">Create and manage different types of promotions.</p>
                <a href="{{ route('promotions.index') }}"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Manage Promotions</a>
            </div>

            <div class="bg-yellow-50 p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-3">Checkout</h2>
                <p class="mb-4">Process orders with automatic promotion application.</p>
                <a href="{{ route('checkout') }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Go
                    to Checkout</a>
            </div>
        </div>
    </div>
@endsection
