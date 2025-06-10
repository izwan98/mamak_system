@extends('layouts.app')

@section('title', 'Promotion Details')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Promotion Details</h1>
            <div class="flex space-x-2">
                <a href="{{ route('promotions.edit', $promotion) }}"
                    class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</a>
                <a href="{{ route('promotions.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to Promotions</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-gray-600">Name</p>
                <p class="font-semibold">{{ $promotion->name }}</p>
            </div>

            <div>
                <p class="text-gray-600">Type</p>
                <p class="font-semibold">
                    @if ($promotion->type === 'buy_x_get_y')
                        Buy X Get Y Free
                    @elseif($promotion->type === 'bulk_discount')
                        Bulk Discount
                    @elseif($promotion->type === 'combo')
                        Combo Promotion
                    @endif
                </p>
            </div>

            <div>
                <p class="text-gray-600">Status</p>
                <p class="font-semibold">
                    @if ($promotion->is_active)
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Active</span>
                    @else
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Inactive</span>
                    @endif
                </p>
            </div>

            <div>
                <p class="text-gray-600">Validity</p>
                <p class="font-semibold">
                    @if ($promotion->start_date && $promotion->end_date)
                        {{ $promotion->start_date->format('Y-m-d') }} to {{ $promotion->end_date->format('Y-m-d') }}
                    @elseif($promotion->start_date)
                        From {{ $promotion->start_date->format('Y-m-d') }}
                    @elseif($promotion->end_date)
                        Until {{ $promotion->end_date->format('Y-m-d') }}
                    @else
                        Always valid
                    @endif
                </p>
            </div>
        </div>

        @if ($promotion->description)
            <div class="mb-6">
                <p class="text-gray-600">Description</p>
                <p>{{ $promotion->description }}</p>
            </div>
        @endif

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">Promotion Rules</h2>

            <div class="bg-gray-50 p-4 rounded-lg">
                @if ($promotion->rules->isEmpty())
                    <p class="text-gray-500">No rules defined for this promotion.</p>
                @else
                    @foreach ($promotion->rules as $rule)
                        <div class="mb-4 last:mb-0">
                            @if ($promotion->type === 'buy_x_get_y')
                                <p>
                                    Buy <span class="font-semibold">{{ $rule->conditions['buy_quantity'] ?? 1 }}</span>
                                    of <span
                                        class="font-semibold">{{ \App\Models\Product::find($rule->conditions['product_id'])->name ?? 'Unknown Product' }}</span>
                                    and get <span class="font-semibold">{{ $rule->actions['free_quantity'] ?? 1 }}</span>
                                    free.
                                </p>
                            @elseif($promotion->type === 'bulk_discount')
                                <p>
                                    Buy <span class="font-semibold">{{ $rule->conditions['min_quantity'] ?? 2 }}</span>
                                    or more of <span
                                        class="font-semibold">{{ \App\Models\Product::find($rule->conditions['product_id'])->name ?? 'Unknown Product' }}</span>
                                    and get them for <span
                                        class="font-semibold">RM{{ number_format($rule->actions['discounted_price'] ?? 0, 2) }}</span>
                                    each.
                                </p>
                            @elseif($promotion->type === 'combo')
                                <p>Buy the following products together:</p>
                                <ul class="list-disc list-inside ml-4 mt-2">
                                    @foreach ($rule->conditions['product_ids'] ?? [] as $productId)
                                        <li>{{ \App\Models\Product::find($productId)->name ?? 'Unknown Product' }}</li>
                                    @endforeach
                                </ul>
                                <p class="mt-2">
                                    and get the combo for <span
                                        class="font-semibold">RM{{ number_format($rule->actions['combo_price'] ?? 0, 2) }}</span>
                                </p>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection
