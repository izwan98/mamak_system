<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home page
Route::get('/', function () {
    return view('home');
})->name('home');

// Product routes
Route::resource('products', ProductController::class);

// Promotion routes
Route::resource('promotions', PromotionController::class);

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/result', [CheckoutController::class, 'result'])->name('checkout.result');
Route::get('/checkout/orders', [CheckoutController::class, 'orders'])->name('checkout.orders');
Route::get('/checkout/orders/{order}', [CheckoutController::class, 'orderDetails'])->name('checkout.order-details');
Route::get('/checkout/test', [CheckoutController::class, 'testCheckout'])->name('checkout.test');
Route::post('/checkout/test', [CheckoutController::class, 'runTest'])->name('checkout.run-test');

// AJAX routes for checkout functionality (not RESTful API)
Route::post('/calculate-totals', [CheckoutController::class, 'calculateTotals'])->name('checkout.calculate-totals');
Route::post('/process-checkout', [CheckoutController::class, 'processAjaxCheckout'])->name('checkout.process-ajax');
