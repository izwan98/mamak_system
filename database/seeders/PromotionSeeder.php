<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Models\PromotionRule;
use App\Models\Product;

class PromotionSeeder extends Seeder
{
    /**
     * Seed the promotions table.
     */
    public function run()
    {
        // Kopi Buy 1 Get 1 Free Promotion
        $kopiProduct = Product::where('code', 'B001')->first();
        if ($kopiProduct) {
            $kopiPromotion = Promotion::create([
                'name' => 'Buy 1 Get 1 Free on Kopi',
                'type' => 'buy_x_get_y',
                'description' => 'Buy 1 Kopi and get 1 free',
                'is_active' => true,
            ]);

            PromotionRule::create([
                'promotion_id' => $kopiPromotion->id,
                'conditions' => [
                    'product_id' => $kopiProduct->id,
                    'buy_quantity' => 1,
                ],
                'actions' => [
                    'free_quantity' => 1,
                ],
            ]);
        }

        // Teh Tarik Buy 1 Get 1 Free Promotion
        $tehProduct = Product::where('code', 'B002')->first();
        if ($tehProduct) {
            $tehPromotion = Promotion::create([
                'name' => 'Buy 1 Get 1 Free on Teh Tarik',
                'type' => 'buy_x_get_y',
                'description' => 'Buy 1 Teh Tarik and get 1 free',
                'is_active' => true,
            ]);

            PromotionRule::create([
                'promotion_id' => $tehPromotion->id,
                'conditions' => [
                    'product_id' => $tehProduct->id,
                    'buy_quantity' => 1,
                ],
                'actions' => [
                    'free_quantity' => 1,
                ],
            ]);
        }

        // Roti Kosong Bulk Discount Promotion
        $rotiProduct = Product::where('code', 'F001')->first();
        if ($rotiProduct) {
            $rotiPromotion = Promotion::create([
                'name' => 'Bulk Discount on Roti Kosong',
                'type' => 'bulk_discount',
                'description' => 'Buy 2 or more Roti Kosong and get them for RM1.20 each',
                'is_active' => true,
            ]);

            PromotionRule::create([
                'promotion_id' => $rotiPromotion->id,
                'conditions' => [
                    'product_id' => $rotiProduct->id,
                    'min_quantity' => 2,
                ],
                'actions' => [
                    'discounted_price' => 1.20,
                ],
            ]);
        }

        // Example Combo Promotion
        if ($kopiProduct && $rotiProduct) {
            $comboPromotion = Promotion::create([
                'name' => 'Kopi & Roti Combo',
                'type' => 'combo',
                'description' => 'Buy Kopi and Roti Kosong together and save!',
                'is_active' => true,
            ]);

            PromotionRule::create([
                'promotion_id' => $comboPromotion->id,
                'conditions' => [
                    'product_ids' => [$kopiProduct->id, $rotiProduct->id],
                ],
                'actions' => [
                    'combo_price' => 3.50, // Instead of 4.00 (2.50 + 1.50)
                ],
            ]);
        }
    }
}
