<?php

namespace App\Services;

class Checkout
{
    protected $pricingRules;
    protected $items = [];
    protected $checkoutService;

    public function __construct($pricingRules = null)
    {
        $this->pricingRules = $pricingRules;
        $this->checkoutService = new CheckoutService();
    }

    public function scan($item)
    {
        $this->items[] = $item;
        $this->checkoutService->scan($item);
        return $this;
    }

    public function getTotal()
    {
        return $this->checkoutService->getTotal();
    }
}
