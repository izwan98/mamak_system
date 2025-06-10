<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CapBay Mamak - @yield('title', 'Home')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>


    <!-- Additional Styles -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="flex flex-col min-h-screen">
        <!-- Navigation -->
        <nav class="bg-blue-600 text-white shadow-md">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('home') }}" class="text-xl font-bold">CapBay Mamak</a>
                        <div class="hidden md:flex space-x-4">
                            <a href="{{ route('products.index') }}" class="hover:text-blue-200">Products</a>
                            <a href="{{ route('promotions.index') }}" class="hover:text-blue-200">Promotions</a>
                            <a href="{{ route('checkout') }}" class="hover:text-blue-200">Checkout</a>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16m-7 6h7"></path>
                            </svg>
                        </button>

                        <!-- Mobile menu -->
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-48 bg-blue-500 py-2 rounded shadow-xl z-20" x-cloak>
                            <a href="{{ route('products.index') }}"
                                class="block px-4 py-2 hover:bg-blue-600">Products</a>
                            <a href="{{ route('promotions.index') }}"
                                class="block px-4 py-2 hover:bg-blue-600">Promotions</a>
                            <a href="{{ route('checkout') }}" class="block px-4 py-2 hover:bg-blue-600">Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main class="flex-grow container mx-auto px-4 py-6">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-blue-800 text-white py-6">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <p>&copy; {{ date('Y') }} CapBay Mamak. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    @stack('scripts')
</body>

</html>
