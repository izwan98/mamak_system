<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Promotion;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\AppliedPromotion;
use Illuminate\Support\Facades\DB;
use Exception;

class CheckoutService
{
    protected $cart = [];
    protected $products = [];
    protected $promotions = [];

    public function __construct()
    {
        // Load all active promotions
        $this->promotions = Promotion::where('is_active', true)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now());
                })
                    ->orWhere(function ($q) {
                        $q->whereNull('start_date')
                            ->whereNull('end_date');
                    })
                    ->orWhere(function ($q) {
                        $q->whereNull('start_date')
                            ->whereDate('end_date', '>=', now());
                    })
                    ->orWhere(function ($q) {
                        $q->whereDate('start_date', '<=', now())
                            ->whereNull('end_date');
                    });
            })
            ->with('rules')
            ->get();
    }

    /**
     * Scan an item and add it to the cart
     */
    public function scan($itemCode)
    {
        $product = Product::where('code', $itemCode)->first();

        if (!$product) {
            throw new Exception("Product with code {$itemCode} not found");
        }

        // Add the product to the cart
        if (!isset($this->cart[$product->id])) {
            $this->cart[$product->id] = [
                'product' => $product,
                'quantity' => 0,
            ];
        }

        $this->cart[$product->id]['quantity']++;
        $this->products[] = $product;

        return $this;
    }

    /**
     * Calculate the total price with promotions
     */
    public function calculateTotal()
    {
        $subtotal = 0;
        $discount = 0;

        // Group cart items by product ID
        $groupedCart = [];
        foreach ($this->cart as $productId => $item) {
            $groupedCart[$productId] = $item;
            $subtotal += $item['product']->price * $item['quantity'];
        }

        // Calculate discounts from promotions
        $appliedPromotions = [];
        $appliedPromotionTypes = [
            'buy_x_get_y' => false,
            'bulk_discount' => false
        ];

        // Process promotions in order of best value for the customer
        $sortedPromotions = $this->promotions->sortByDesc(function ($promotion) {
            // Assign a value score to each promotion type
            if ($promotion->type === 'combo') {
                return 3; // Highest priority
            } elseif ($promotion->type === 'buy_x_get_y') {
                return 2; // Medium priority
            } elseif ($promotion->type === 'bulk_discount') {
                return 1; // Lowest priority
            }
            return 0;
        });

        // $sortedPromotions = $this->promotions;
        foreach ($sortedPromotions as $promotion) {
            // Only consider active promotions
            if (!$promotion->is_active) {
                continue;
            }

            // Process Buy X Get Y Free promotions - only apply once
            if ($promotion->type === 'buy_x_get_y' && !$appliedPromotionTypes['buy_x_get_y']) {
                $bestDiscount = 0;
                $bestPromotion = null;
                $bestAffectedItems = [];
                $data = [];
                // Find the best Buy X Get Y promotion for the customer
                foreach ($promotion->rules as $rule) {
                    $productId = $rule->conditions['product_id'] ?? null;
                    $buyQuantity = $rule->conditions['buy_quantity'] ?? 1;
                    $getFreeQuantity = $rule->actions['free_quantity'] ?? 1;

                    if ($productId && isset($groupedCart[$productId]) && $groupedCart[$productId]['quantity'] >= ($buyQuantity)) {
                        $product = $groupedCart[$productId]['product'];
                        $totalQuantity = $groupedCart[$productId]['quantity'];

                        // For Buy X Get Y Free, we only apply it once per transaction
                        // So we'll apply it to the first set of items
                        $freeItems = min($getFreeQuantity, $totalQuantity - $buyQuantity);
                        $freeItems = max(0, $freeItems); // Ensure non-negative

                        // Calculate discount
                        $itemDiscount = $freeItems * $product->price;
                        $data['itemDiscount'] = $itemDiscount;
                        // Check if this is the best discount
                        if ($itemDiscount > $bestDiscount) {
                            $bestDiscount = $itemDiscount;
                            $bestPromotion = $promotion;
                            $bestAffectedItems = [$productId];
                        }
                    }
                }


                // Apply the best discount
                if ($bestDiscount > 0 && $bestPromotion) {
                    $discount += $bestDiscount;
                    $appliedPromotionTypes['buy_x_get_y'] = true;

                    $appliedPromotions[] = [
                        'promotion_id' => $bestPromotion->id,
                        'promotion_name' => $bestPromotion->name,
                        'discount_amount' => $bestDiscount,
                        'affected_items' => $bestAffectedItems,
                    ];
                }
            }
            // Process Bulk Discount promotions - only apply once
            elseif ($promotion->type === 'bulk_discount' && !$appliedPromotionTypes['bulk_discount']) {
                $bestDiscount = 0;
                $bestPromotion = null;
                $bestAffectedItems = [];

                // Find the best Bulk Discount promotion for the customer
                foreach ($promotion->rules as $rule) {
                    $productId = $rule->conditions['product_id'] ?? null;
                    $minQuantity = $rule->conditions['min_quantity'] ?? 2;
                    $discountedPrice = $rule->actions['discounted_price'] ?? null;

                    if ($productId && isset($groupedCart[$productId]) && $groupedCart[$productId]['quantity'] >= $minQuantity && $discountedPrice !== null) {
                        $product = $groupedCart[$productId]['product'];
                        $quantity = $groupedCart[$productId]['quantity'];

                        // Calculate discount
                        $originalPrice = $product->price * $quantity;
                        $discountedTotal = $discountedPrice * $quantity;
                        $itemDiscount = $originalPrice - $discountedTotal;

                        // Check if this is the best discount
                        if ($itemDiscount > $bestDiscount) {
                            $bestDiscount = $itemDiscount;
                            $bestPromotion = $promotion;
                            $bestAffectedItems = [$productId];
                        }
                    }
                }

                // Apply the best discount
                if ($bestDiscount > 0 && $bestPromotion) {
                    $discount += $bestDiscount;
                    $appliedPromotionTypes['bulk_discount'] = true;

                    $appliedPromotions[] = [
                        'promotion_id' => $bestPromotion->id,
                        'promotion_name' => $bestPromotion->name,
                        'discount_amount' => $bestDiscount,
                        'affected_items' => $bestAffectedItems,
                    ];
                }
            }
            // Process Combo promotions - these can apply multiple times
            elseif ($promotion->type === 'combo') {
                foreach ($promotion->rules as $rule) {
                    $productIds = $rule->conditions['product_ids'] ?? [];
                    $comboPrice = $rule->actions['combo_price'] ?? null;

                    // Check if all products in the combo are in the cart
                    $comboComplete = true;
                    foreach ($productIds as $productId) {
                        if (!isset($groupedCart[$productId]) || $groupedCart[$productId]['quantity'] < 1) {
                            $comboComplete = false;
                            break;
                        }
                    }

                    if ($comboComplete && $comboPrice !== null) {
                        // Calculate original price for these items
                        $originalComboPrice = 0;
                        foreach ($productIds as $productId) {
                            $originalComboPrice += $groupedCart[$productId]['product']->price;
                        }

                        // Calculate discount
                        $itemDiscount = $originalComboPrice - $comboPrice;

                        if ($itemDiscount > 0) {
                            $discount += $itemDiscount;

                            $appliedPromotions[] = [
                                'promotion_id' => $promotion->id,
                                'promotion_name' => $promotion->name,
                                'discount_amount' => $itemDiscount,
                                'affected_items' => $productIds,
                            ];
                        }
                    }
                }
            }
        }

        $total = $subtotal - $discount;

        // Ensure total is not negative
        $total = max(0, $total);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'appliedPromotions' => $appliedPromotions,
        ];
    }

    /**
     * Get the total price
     */
    public function getTotal()
    {
        $result = $this->calculateTotal();
        return $result['total'];
    }

    /**
     * Process a checkout and create an order
     */
    public function checkout()
    {
        if (empty($this->cart)) {
            throw new Exception("Cart is empty");
        }

        $result = $this->calculateTotal();

        try {
            DB::beginTransaction();

            // Create the order
            $order = Order::create([
                'subtotal' => $result['subtotal'],
                'discount' => $result['discount'],
                'total' => $result['total'],
            ]);

            // Create order items
            foreach ($this->cart as $productId => $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_code' => $product->code,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                    'discount' => 0, // This will be updated after applying promotions
                    'total' => $product->price * $quantity,
                ]);
            }

            // Create applied promotions
            foreach ($result['appliedPromotions'] as $appliedPromo) {
                AppliedPromotion::create([
                    'order_id' => $order->id,
                    'promotion_id' => $appliedPromo['promotion_id'],
                    'promotion_name' => $appliedPromo['promotion_name'],
                    'discount_amount' => $appliedPromo['discount_amount'],
                    'affected_items' => $appliedPromo['affected_items'],
                ]);
            }

            DB::commit();

            // Clear the cart
            $this->cart = [];
            $this->products = [];

            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
