@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6" x-data="checkoutApp()">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Checkout</h1>
            <div class="flex space-x-2">
                <a href="{{ route('checkout.test') }}"
                    class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Test Checkout</a>
                <a href="{{ route('checkout.orders') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">View Orders</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Product Selection Panel -->
            <div class="md:col-span-2">
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h2 class="text-lg font-semibold mb-4">Products</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($products as $product)
                            <div
                                class="bg-white p-3 rounded-lg border shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="mb-2">
                                    <span class="text-gray-600 text-sm">{{ $product->code }}</span>
                                    <h3 class="font-semibold">{{ $product->name }}</h3>
                                    <p class="text-blue-600">{{ $product->formatted_price }}</p>
                                </div>
                                <button
                                    @click="addItem('{{ $product->code }}', '{{ $product->name }}', {{ $product->price }})"
                                    class="w-full bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">
                                    Add to Cart
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Manual Checkout Form -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold mb-4">Manual Checkout</h2>

                    <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="manual_items" class="block text-gray-700 font-medium mb-2">Enter Item Codes (comma
                                separated)</label>
                            <div class="flex">
                                <input type="text" name="items" id="manual_items"
                                    class="flex-1 border-gray-300 rounded-l-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    placeholder="B001,F001,B002">
                                <button type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-r hover:bg-blue-700">Process</button>
                            </div>
                            @error('items')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </form>

                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mt-4">
                        <p class="font-medium">Test Cases from Requirements:</p>
                        <ul class="list-disc list-inside mt-2 text-sm">
                            <li>B001,F001,B002,B001,F001 = RM6.9</li>
                            <li>B002,B002,F001 = RM3.5</li>
                            <li>B001,B001,B002 = RM4.5</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Cart Panel -->
            <div class="bg-gray-50 p-4 rounded-lg max-h-screen">
                <h2 class="text-lg font-semibold mb-4">Cart</h2>

                <div x-show="cart.length === 0" class="text-gray-500 text-center py-4">
                    Your cart is empty. Add some items!
                </div>

                <template x-if="cart.length > 0">
                    <div>
                        <div class="bg-white rounded-lg border mb-4 max-h-64 overflow-y-auto">
                            <ul class="divide-y">
                                <template x-for="(item, index) in cart" :key="index">
                                    <li class="p-3 flex justify-between items-center">
                                        <div>
                                            <span x-text="item.code" class="text-gray-600 text-sm"></span>
                                            <p x-text="item.name" class="font-medium"></p>
                                            <p x-text="`RM${item.price.toFixed(2)}`" class="text-blue-600 text-sm"></p>
                                        </div>
                                        <button @click="removeItem(index)" class="text-red-500 hover:text-red-700"
                                            title="Remove item">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <div class="flex justify-between py-1">
                                <span>Subtotal:</span>
                                <span x-text="`RM${subtotal.toFixed(2)}`"></span>
                            </div>
                            <div class="flex justify-between py-1 text-green-600">
                                <span>Discount:</span>
                                <span x-text="`-RM${discount.toFixed(2)}`"></span>
                            </div>
                            <div class="flex justify-between py-1 font-semibold text-lg border-t mt-2 pt-2">
                                <span>Total:</span>
                                <span x-text="`RM${total.toFixed(2)}`"></span>
                            </div>
                        </div>

                        <div x-show="appliedPromotions.length > 0" class="mb-4">
                            <h3 class="font-medium mb-2">Applied Promotions:</h3>
                            <ul class="text-sm text-green-600 list-disc list-inside">
                                <template x-for="promo in appliedPromotions" :key="promo.promotion_name">
                                    <li x-text="promo.promotion_name"></li>
                                </template>
                            </ul>
                        </div>

                        <div class="flex space-x-2">
                            <button @click="clearCart()"
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm flex-1">
                                Clear Cart
                            </button>
                            <button @click="checkout()"
                                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm flex-1"
                                :disabled="cart.length === 0"
                                :class="{ 'opacity-50 cursor-not-allowed': cart.length === 0 }">
                                Checkout
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Order Completed Modal -->
        <div x-show="showOrderCompleted"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" x-cloak>
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
                <h2 class="text-xl font-bold mb-4">Order Completed</h2>
                <p class="mb-4">Your order has been processed successfully!</p>
                <div class="bg-gray-50 p-3 rounded mb-4">
                    <div class="flex justify-between py-1">
                        <span>Order ID:</span>
                        <span x-text="completedOrder.id"></span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span>Total:</span>
                        <span x-text="`RM${completedOrder.total.toFixed(2)}`"></span>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button @click="closeOrderModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function checkoutApp() {
            return {
                cart: [],
                cartItemsCount: 0,
                subtotal: 0,
                discount: 0,
                total: 0,
                appliedPromotions: [],
                showOrderCompleted: false,
                completedOrder: null,

                init() {
                    this.loadCartFromStorage();
                    this.calculateTotals();
                },

                addItem(code, name, price) {
                    this.cart.push({
                        code: code,
                        name: name,
                        price: parseFloat(price)
                    });
                    this.calculateTotals();
                    this.saveCartToStorage();
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                    this.calculateTotals();
                    this.saveCartToStorage();
                },

                clearCart() {
                    this.cart = [];
                    this.subtotal = 0;
                    this.discount = 0;
                    this.total = 0;
                    this.appliedPromotions = [];
                    localStorage.removeItem('cart');
                },

                async calculateTotals() {
                    this.cartItemsCount = this.cart.length;

                    if (this.cart.length === 0) {
                        this.subtotal = 0;
                        this.discount = 0;
                        this.total = 0;
                        this.appliedPromotions = [];
                        return;
                    }

                    // Calculate subtotal (without promotions)
                    this.subtotal = this.cart.reduce((sum, item) => sum + item.price, 0);

                    // Calculate discounts with promotions from server
                    try {
                        const response = await fetch('{{ route('checkout.calculate-totals') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                items: this.cart.map(item => item.code)
                            })
                        });

                        if (!response.ok) {
                            throw new Error('Failed to calculate totals');
                        }

                        const data = await response.json();
                        this.discount = data.discount;
                        this.total = data.total;
                        this.appliedPromotions = data.appliedPromotions || [];
                    } catch (error) {
                        console.error('Error calculating totals:', error);
                        // Fallback calculation
                        this.total = this.subtotal;
                        this.discount = 0;
                    }
                },

                async checkout() {
                    if (this.cart.length === 0) {
                        return;
                    }

                    try {
                        const response = await fetch('{{ route('checkout.process-ajax') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                items: this.cart.map(item => item.code)
                            })
                        });

                        if (!response.ok) {
                            throw new Error('Checkout failed');
                        }

                        const data = await response.json();
                        this.completedOrder = data.order;
                        this.showOrderCompleted = true;
                        this.clearCart();
                    } catch (error) {
                        console.error('Error during checkout:', error);
                        alert('Checkout failed. Please try again.');
                    }
                },

                closeOrderModal() {
                    this.showOrderCompleted = false;
                    this.completedOrder = null;
                    window.location.href = '{{ route('checkout.orders') }}';
                },

                saveCartToStorage() {
                    localStorage.setItem('cart', JSON.stringify(this.cart));
                },

                loadCartFromStorage() {
                    const savedCart = localStorage.getItem('cart');
                    if (savedCart) {
                        try {
                            this.cart = JSON.parse(savedCart);
                        } catch (error) {
                            console.error('Error loading cart from storage:', error);
                            this.cart = [];
                        }
                    }
                }
            };
        }
    </script>
@endpush
