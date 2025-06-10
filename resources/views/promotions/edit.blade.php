@extends('layouts.app')

@section('title', 'Edit Promotion')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Promotion</h1>
            <a href="{{ route('promotions.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back
                to Promotions</a>
        </div>

        <form action="{{ route('promotions.update', $promotion) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-gray-700 font-medium mb-2">Promotion Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $promotion->name) }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-gray-700 font-medium mb-2">Promotion Type</label>
                    <input type="text"
                        value="{{ $promotion->type === 'buy_x_get_y' ? 'Buy X Get Y Free' : ($promotion->type === 'bulk_discount' ? 'Bulk Discount' : 'Combo Promotion') }}"
                        class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm" readonly>
                    <input type="hidden" name="type" value="{{ $promotion->type }}">
                </div>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description', $promotion->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_date" class="block text-gray-700 font-medium mb-2">Start Date (Optional)</label>
                    <input type="date" name="start_date" id="start_date"
                        value="{{ old('start_date', $promotion->start_date ? $promotion->start_date->format('Y-m-d') : '') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-gray-700 font-medium mb-2">End Date (Optional)</label>
                    <input type="date" name="end_date" id="end_date"
                        value="{{ old('end_date', $promotion->end_date ? $promotion->end_date->format('Y-m-d') : '') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2">Active</span>
                </label>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update
                    Promotion</button>
            </div>
        </form>
    </div>
@endsection
