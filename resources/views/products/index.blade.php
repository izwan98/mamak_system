@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Products</h1>
            <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add
                New Product</a>
        </div>

        @if ($products->isEmpty())
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p>No products found. Please add some products.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left">Code</th>
                            <th class="py-3 px-4 text-left">Name</th>
                            <th class="py-3 px-4 text-left">Price</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $product->code }}</td>
                                <td class="py-3 px-4">{{ $product->name }}</td>
                                <td class="py-3 px-4">{{ $product->formatted_price }}</td>
                                <td class="py-3 px-4">
                                    @if (!$product->canDelete)
                                        <span
                                            class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Used
                                            in orders</span>
                                    @else
                                        <span
                                            class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Not
                                            used</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('products.edit', $product) }}"
                                            class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">Edit</a>

                                        @if ($product->canDelete)
                                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this product?');"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Delete</button>
                                            </form>
                                        @else
                                            <button
                                                class="bg-gray-300 text-gray-500 px-3 py-1 rounded text-sm cursor-not-allowed"
                                                title="This product cannot be deleted because it has been used in orders">Delete</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
