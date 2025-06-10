@extends('layouts.app')

@section('title', 'Promotions')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Promotions</h1>
            <a href="{{ route('promotions.create') }}"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add New Promotion</a>
        </div>

        @if ($promotions->isEmpty())
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p>No promotions found. Please add some promotions.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left">Name</th>
                            <th class="py-3 px-4 text-left">Type</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-left">Validity</th>
                            <th class="py-3 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($promotions as $promotion)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $promotion->name }}</td>
                                <td class="py-3 px-4">
                                    @if ($promotion->type === 'buy_x_get_y')
                                        Buy X Get Y Free
                                    @elseif($promotion->type === 'bulk_discount')
                                        Bulk Discount
                                    @elseif($promotion->type === 'combo')
                                        Combo Promotion
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($promotion->is_active)
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Active</span>
                                    @else
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($promotion->start_date && $promotion->end_date)
                                        {{ $promotion->start_date->format('Y-m-d') }} to
                                        {{ $promotion->end_date->format('Y-m-d') }}
                                    @elseif($promotion->start_date)
                                        From {{ $promotion->start_date->format('Y-m-d') }}
                                    @elseif($promotion->end_date)
                                        Until {{ $promotion->end_date->format('Y-m-d') }}
                                    @else
                                        Always valid
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('promotions.show', $promotion) }}"
                                            class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">View</a>
                                        <a href="{{ route('promotions.edit', $promotion) }}"
                                            class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">Edit</a>
                                        <form action="{{ route('promotions.destroy', $promotion) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this promotion?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Delete</button>
                                        </form>
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
