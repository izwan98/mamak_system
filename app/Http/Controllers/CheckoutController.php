<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Services\Checkout;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = Product::all();
        return view('checkout.index', compact('products'));
    }

    /**
     * Calculate totals for AJAX checkout
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateTotals(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $checkoutService = new CheckoutService();

            // Scan all items
            foreach ($request->items as $item) {
                $checkoutService->scan($item);
            }

            // Calculate the total with promotions
            $result = $checkoutService->calculateTotal();

            return response()->json([
                'subtotal' => $result['subtotal'],
                'discount' => $result['discount'],
                'total' => $result['total'],
                'appliedPromotions' => $result['appliedPromotions'],
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process checkout via AJAX
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processAjaxCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $checkoutService = new CheckoutService();

            // Scan all items
            foreach ($request->items as $item) {
                $checkoutService->scan($item);
            }

            // Process the checkout
            $order = $checkoutService->checkout();

            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'subtotal' => $order->subtotal,
                    'discount' => $order->discount,
                    'total' => $order->total,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process a checkout manually without using AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'items' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $itemCodes = explode(',', str_replace(' ', '', $request->items));
            $checkout = new Checkout();

            foreach ($itemCodes as $item) {
                $checkout->scan($item);
            }
            $total = $checkout->getTotal();

            // Create an order in the database
            $checkoutService = new CheckoutService();
            foreach ($itemCodes as $item) {
                $checkoutService->scan($item);
            }
            $order = $checkoutService->checkout();

            return redirect()->route('checkout.result')
                ->with('total', $total)
                ->with('items', $itemCodes)
                ->with('order_id', $order->id);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Checkout failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the checkout result page.
     *
     * @return \Illuminate\View\View
     */
    public function result()
    {
        if (!session()->has('total') || !session()->has('items')) {
            return redirect()->route('checkout');
        }

        $total = session('total');
        $itemCodes = session('items');
        $orderId = session('order_id');

        // Get item details
        $items = [];
        foreach ($itemCodes as $code) {
            $product = Product::where('code', $code)->first();
            if ($product) {
                $items[] = $product;
            }
        }

        return view('checkout.result', compact('total', 'items', 'orderId'));
    }

    /**
     * Display recent orders.
     *
     * @return \Illuminate\View\View
     */
    public function orders()
    {
        $orders = Order::latest()->paginate(10);
        return view('checkout.orders', compact('orders'));
    }

    /**
     * Display a specific order details.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function orderDetails(Order $order)
    {
        $order->load(['items', 'appliedPromotions']);
        return view('checkout.order_details', compact('order'));
    }

    /**
     * Show a form to test the checkout with different item lists.
     *
     * @return \Illuminate\View\View
     */
    public function testCheckout()
    {
        $products = Product::all();
        $testCases = [
            [
                'name' => 'Test Case 1',
                'items' => ['B001', 'F001', 'B002', 'B001', 'F001'],
                'expected' => 6.9
            ],
            [
                'name' => 'Test Case 2',
                'items' => ['B002', 'B002', 'F001'],
                'expected' => 3.5
            ],
            [
                'name' => 'Test Case 3',
                'items' => ['B001', 'B001', 'B002'],
                'expected' => 4.5
            ]
        ];

        return view('checkout.test', compact('products', 'testCases'));
    }

    /**
     * Run a test checkout and show the results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function runTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_items' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $itemCodes = explode(',', str_replace(' ', '', $request->test_items));

            $checkout = new Checkout();

            foreach ($itemCodes as $code) {
                $checkout->scan($code);
            }
            $total = $checkout->getTotal();

            return redirect()->route('checkout.test')
                ->with('test_result', [
                    'items' => $itemCodes,
                    'total' => $total
                ]);
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Test failed: ' . $e->getMessage())
                ->withInput();
        }
    }
}
