@extends('layouts.app')

@section('title', 'Test Checkout')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Test Checkout</h1>
            <a href="{{ route('checkout') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Go to
                Checkout</a>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-3">Test Cases from Requirements</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                @foreach ($testCases as $case)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium mb-2">{{ $case['name'] }}</h3>
                        <p class="text-sm mb-2">Items: <span class="font-mono">{{ implode(', ', $case['items']) }}</span>
                        </p>
                        <p class="text-sm">Expected: <span
                                class="font-semibold">RM{{ number_format($case['expected'], 2) }}</span></p>
                        <form action="{{ route('checkout.run-test') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="test_items" value="{{ implode(',', $case['items']) }}">
                            <button type="submit"
                                class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Run Test</button>
                        </form>
                    </div>
                @endforeach
            </div>

            @if (session('test_result'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                    <h3 class="font-medium text-green-700 mb-2">Test Result</h3>
                    <p>Items: <span class="font-mono">{{ implode(', ', session('test_result')['items']) }}</span></p>
                    <p>Total: <span class="font-semibold">RM{{ number_format(session('test_result')['total'], 2) }}</span>
                    </p>
                </div>
            @endif
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-3">Custom Test</h2>

            <form action="{{ route('checkout.run-test') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="test_items" class="block text-gray-700 font-medium mb-2">Enter Items (comma
                        separated)</label>
                    <input type="text" name="test_items" id="test_items"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="B001,F001,B002">
                    @error('test_items')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Run Test</button>
            </form>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-3">Available Products</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4 text-left">Code</th>
                            <th class="py-2 px-4 text-left">Name</th>
                            <th class="py-2 px-4 text-left">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="border-t">
                                <td class="py-2 px-4 font-mono">{{ $product->code }}</td>
                                <td class="py-2 px-4">{{ $product->name }}</td>
                                <td class="py-2 px-4">{{ $product->formatted_price }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
