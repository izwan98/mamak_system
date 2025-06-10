@extends('layouts.app')

@section('title', 'Create Promotion')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Create Promotion</h1>
            <a href="{{ route('promotions.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back
                to Promotions</a>
        </div>

        <form action="{{ route('promotions.store') }}" method="POST" x-data="{ promotionType: '{{ old('type', 'buy_x_get_y') }}' }">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-gray-700 font-medium mb-2">Promotion Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-gray-700 font-medium mb-2">Promotion Type</label>
                    <select name="type" id="type" x-model="promotionType"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('type') border-red-500 @enderror"
                        required>
                        <option value="buy_x_get_y">Buy X Get Y Free</option>
                        <option value="bulk_discount">Bulk Discount</option>
                        <option value="combo">Combo Promotion</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_date" class="block text-gray-700 font-medium mb-2">Start Date (Optional)</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-gray-700 font-medium mb-2">End Date (Optional)</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2">Active</span>
                </label>
            </div>

            <!-- Buy X Get Y Free Promotion Form -->
            <div x-show="promotionType === 'buy_x_get_y'" class="bg-blue-50 p-4 rounded-lg mb-6">
                <h2 class="text-lg font-semibold mb-4">Buy X Get Y Free Promotion</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="product_id_x_y" class="block text-gray-700 font-medium mb-2">Product</label>
                        <select name="product_id" id="product_id_x_y"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('product_id') border-red-500 @enderror"
                            x-bind:required="promotionType === 'buy_x_get_y'">
                            <option value="">Select a product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->code }}) - {{ $product->formatted_price }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="buy_quantity" class="block text-gray-700 font-medium mb-2">Buy Quantity</label>
                        <input type="number" name="buy_quantity" id="buy_quantity" value="{{ old('buy_quantity', 1) }}"
                            min="1"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('buy_quantity') border-red-500 @enderror"
                            x-bind:required="promotionType === 'buy_x_get_y'">
                        @error('buy_quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="free_quantity" class="block text-gray-700 font-medium mb-2">Free Quantity</label>
                        <input type="number" name="free_quantity" id="free_quantity" value="{{ old('free_quantity', 1) }}"
                            min="1"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('free_quantity') border-red-500 @enderror"
                            x-bind:required="promotionType === 'buy_x_get_y'">
                        @error('free_quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Bulk Discount Promotion Form -->
            <div x-show="promotionType === 'bulk_discount'" class="bg-green-50 p-4 rounded-lg mb-6">
                <h2 class="text-lg font-semibold mb-4">Bulk Discount Promotion</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="product_id_bulk" class="block text-gray-700 font-medium mb-2">Product</label>
                        <select name="product_id" id="product_id_bulk"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('product_id') border-red-500 @enderror"
                            x-bind:required="promotionType === 'bulk_discount'"
                            x-bind:disabled="promotionType !== 'bulk_discount'">
                            <option value="">Select a product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->code }}) - {{ $product->formatted_price }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="min_quantity" class="block text-gray-700 font-medium mb-2">Minimum Quantity</label>
                        <input type="number" name="min_quantity" id="min_quantity"
                            value="{{ old('min_quantity', 2) }}" min="1"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('min_quantity') border-red-500 @enderror"
                            x-bind:required="promotionType === 'bulk_discount'"
                            x-bind:disabled="promotionType !== 'bulk_discount'">
                        @error('min_quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="discounted_price" class="block text-gray-700 font-medium mb-2">Discounted Price
                            (RM)</label>
                        <input type="number" name="discounted_price" id="discounted_price"
                            value="{{ old('discounted_price') }}" step="0.01" min="0"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('discounted_price') border-red-500 @enderror"
                            x-bind:required="promotionType === 'bulk_discount'"
                            x-bind:disabled="promotionType !== 'bulk_discount'">
                        @error('discounted_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Combo Promotion Form -->
            <div x-show="promotionType === 'combo'" class="bg-yellow-50 p-4 rounded-lg mb-6">
                <h2 class="text-lg font-semibold mb-4">Combo Promotion</h2>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Select Products for Combo</label>
                    <div class="bg-white p-3 rounded border max-h-40 overflow-y-auto">
                        @foreach ($products as $product)
                            <div class="mb-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        x-bind:disabled="promotionType !== 'combo'">
                                    <span class="ml-2">{{ $product->name }} ({{ $product->code }}) -
                                        {{ $product->formatted_price }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('product_ids')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="combo_price" class="block text-gray-700 font-medium mb-2">Combo Price (RM)</label>
                    <input type="number" name="combo_price" id="combo_price" value="{{ old('combo_price') }}"
                        step="0.01" min="0"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('combo_price') border-red-500 @enderror"
                        x-bind:required="promotionType === 'combo'" x-bind:disabled="promotionType !== 'combo'">
                    @error('combo_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Create
                    Promotion</button>
            </div>
        </form>
    </div>
@endsection
